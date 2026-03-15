<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSale;
use App\Models\StoreStock;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\StoreStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StockController extends Controller
{
    protected $storeStockService;

    public function __construct(StoreStockService $storeStockService)
    {
        $this->storeStockService = $storeStockService;
    }

    /**
     * Display store stock
     */
    public function index()
    {
        $storeId = auth()->id();
        $stocks = $this->storeStockService->getStoreStock($storeId);

        return view('store.stock.index', compact('stocks'));
    }

    /**
     * Record sale and update stock
     */
    public function recordSale(Request $request, StoreStock $stock)
    {
        // Ensure store can only update their own stock
        if ($stock->store_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'doctor_code' => 'nullable|string',
            'prescription' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $quantity = $validated['quantity'];

        // Extra safety check
        if ($quantity > $stock->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock available.']);
        }

        if ($stock->available_stock < $quantity) {
            return back()->with('error', 'Insufficient stock available. Available: ' . $stock->available_stock);
        }

        // STEP 2 — FORCE VALIDATION IF CODE OR PHONE ENTERED
        $doctor = null;
        
        if (!empty($validated['doctor_code']) && trim($validated['doctor_code']) !== '') {
            $enteredIdentifier = trim($validated['doctor_code']);
            
            // Normalize phone number: remove non-digits
            $normalizedPhone = preg_replace('/\D/', '', $enteredIdentifier);
            
            $doctor = User::where(function ($query) use ($enteredIdentifier, $normalizedPhone) {
                // Match by unique_code (case-insensitive)
                $query->whereRaw('LOWER(unique_code) = ?', [strtolower($enteredIdentifier)])
                    // OR match by phone (original or normalized)
                    ->orWhere('phone', $enteredIdentifier)
                    ->orWhere('phone', $normalizedPhone);
            })
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Doctor');
            })
            ->first();

            // Hard validation - reject if code/phone entered but not found
            if (!$doctor) {
                return back()->withErrors([
                    'doctor_code' => 'Invalid Doctor Code or Phone: [' . $enteredIdentifier . ']. Please check and try again.'
                ])->withInput();
            }
        }

        // Store prescription file
        $prescriptionPath = $request->file('prescription')->store('prescriptions', 'public');

        // Reduce stock
        $success = $this->storeStockService->recordSale($stock->store_id, $stock->product_id, $quantity);

        if ($success) {
            // Create store sale record
            StoreSale::create([
                'store_id' => $stock->store_id,
                'product_id' => $stock->product_id,
                'doctor_id' => $doctor ? $doctor->id : null,
                'quantity' => $quantity,
                'prescription_path' => $prescriptionPath,
                'sale_type' => $doctor ? 'doctor_referral' : 'store_direct',
            ]);

            /* CREATE ORDER FOR DOCTOR REFERRAL */
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => auth()->id(), // store user
                'doctor_id' => $doctor ? $doctor->id : null,
                'store_id' => $stock->store_id,
                'referral_code' => $doctor ? $doctor->unique_code : null,
                'status' => 'delivered',
                'sale_type' => $doctor ? 'referral' : 'store_direct',
                'total_amount' => $stock->product->price * $quantity,
                'prescription_required' => 0,
                'delivered_at' => now(),
            ]);

            // Create order item for proper aggregation
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $stock->product_id,
                'quantity' => $quantity,
                'price' => $stock->product->price,
                'subtotal' => $stock->product->price * $quantity,
            ]);

            ActivityLogService::log(
                'stock_sale_recorded',
                $stock,
                "Recorded sale of {$quantity} units of {$stock->product->name}" . ($doctor ? " (Referred by Dr. {$doctor->name})" : " (Store Direct)")
            );
            
            // Invalidate leaderboard caches since new delivered order affects rankings
            Cache::forget('leaderboard_monthly_' . now()->format('Y_m'));
            Cache::forget('leaderboard_all_time');

            $successMessage = 'Sale recorded successfully. Stock updated.';
            
            // Show doctor confirmation if referral
            if ($doctor) {
                $successMessage .= ' | Referral: Dr. ' . $doctor->name . ' (ID: ' . $doctor->id . ')';
            } else {
                $successMessage .= ' | Store Direct Sale';
            }

            return back()->with('success', $successMessage);
        }

        return back()->with('error', 'Failed to record sale.');
    }

    /**
     * Show the bulk sale form — lists all in-stock products for the store.
     */
    public function bulkSaleForm()
    {
        $storeId = auth()->id();
        $stocks   = $this->storeStockService->getStoreStock($storeId)
            ->filter(fn ($s) => $s->available_stock > 0)
            ->values();

        return view('store.bulk-sale', compact('stocks'));
    }

    /**
     * Record multiple product sales in one atomic transaction.
     *
     * STEP 3 — Validation:
     *   products[]   required array
     *   quantities[] required array, each integer >= 1
     *
     * STEP 2 — DB Transaction:
     *   Loop each product → recordSale() → StoreSale → Order + OrderItem
     *   Any failure rolls back every change (atomicity).
     *
     * STEP 6 — Safety:
     *   StoreStock::recordSale() returns false if available_stock < requested qty.
     *   We throw an exception inside the transaction to trigger full rollback.
     */
    public function storeBulkSale(Request $request)
    {
        $storeId = auth()->id();

        // --- Validation ---
        $validated = $request->validate([
            'products'          => 'required|array|min:1',
            'products.*'        => 'required|integer|exists:store_stocks,product_id',
            'quantities'        => 'required|array|min:1',
            'quantities.*'      => 'required|integer|min:1',
            'doctor_code'       => 'nullable|string',
            'prescription'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Resolve entries: zip products[] + quantities[], skip qty=0
        $entries = collect($validated['products'])
            ->zip($validated['quantities'])
            ->filter(fn ($pair) => (int) $pair[1] > 0)
            ->values();

        if ($entries->isEmpty()) {
            return back()->with('error', 'Please enter at least one quantity greater than 0.');
        }

        // --- Doctor lookup (same logic as single recordSale) ---
        $doctor = null;
        if (!empty($validated['doctor_code']) && trim($validated['doctor_code']) !== '') {
            $enteredIdentifier = trim($validated['doctor_code']);
            $normalizedPhone   = preg_replace('/\D/', '', $enteredIdentifier);

            $doctor = User::where(function ($q) use ($enteredIdentifier, $normalizedPhone) {
                $q->whereRaw('LOWER(unique_code) = ?', [strtolower($enteredIdentifier)])
                  ->orWhere('phone', $enteredIdentifier)
                  ->orWhere('phone', $normalizedPhone);
            })
            ->whereHas('roles', fn ($q) => $q->where('name', 'Doctor'))
            ->first();

            if (!$doctor) {
                return back()->withErrors([
                    'doctor_code' => 'Invalid Doctor Code or Phone: [' . $enteredIdentifier . ']. Please check and try again.',
                ])->withInput();
            }
        }

        // Store prescription once — shared across all line items
        $prescriptionPath = $request->file('prescription')->store('prescriptions', 'public');

        try {
            DB::transaction(function () use ($entries, $storeId, $doctor, $prescriptionPath) {
                foreach ($entries as [$productId, $quantity]) {
                    $quantity = (int) $quantity;

                    // Fetch product for pricing and logging
                    $product = Product::find($productId);

                    // Decrement stock using existing method
                    // Stock validation occurs inside recordSale() with proper locking
                    $success = $this->storeStockService->recordSale($storeId, $productId, $quantity);

                    if (!$success) {
                        throw new \RuntimeException(
                            "Failed to update stock for product ID {$productId}."
                        );
                    }

                    // --- StoreSale record ---
                    StoreSale::create([
                        'store_id'          => $storeId,
                        'product_id'        => $productId,
                        'doctor_id'         => $doctor ? $doctor->id : null,
                        'quantity'          => $quantity,
                        'prescription_path' => $prescriptionPath,
                        'sale_type'         => $doctor ? 'doctor_referral' : 'store_direct',
                    ]);

                    // --- Order + OrderItem for aggregation ---
                    $order = Order::create([
                        'order_number'         => 'ORD-' . strtoupper(Str::random(8)),
                        'user_id'              => $storeId,
                        'doctor_id'            => $doctor ? $doctor->id : null,
                        'store_id'             => $storeId,
                        'referral_code'        => $doctor ? $doctor->unique_code : null,
                        'status'               => 'delivered',
                        'sale_type'            => $doctor ? 'referral' : 'store_direct',
                        'total_amount'         => $product->price * $quantity,
                        'prescription_required' => 0,
                        'delivered_at'         => now(),
                    ]);

                    OrderItem::create([
                        'order_id'   => $order->id,
                        'product_id' => $productId,
                        'quantity'   => $quantity,
                        'price'      => $product->price,
                        'subtotal'   => $product->price * $quantity,
                    ]);

                    ActivityLogService::log(
                        'bulk_sale_recorded',
                        $product,
                        "Bulk sale: {$quantity} units of {$product->name}"
                        . ($doctor ? " (Ref: Dr. {$doctor->name})" : ' (Store Direct)')
                    );
                }

                // Invalidate leaderboard caches
                Cache::forget('leaderboard_monthly_' . now()->format('Y_m'));
                Cache::forget('leaderboard_all_time');
            });
        } catch (\RuntimeException $e) {
            // Rollback already happened — surface the message to the store owner
            return back()->with('error', 'Bulk sale failed and was rolled back. Reason: ' . $e->getMessage())->withInput();
        }

        $doctorMsg = $doctor ? ' | Referral: Dr. ' . $doctor->name : ' | Store Direct Sale';
        $count     = $entries->count();

        return redirect()->route('store.stock.index')
            ->with('success', "Bulk sale recorded successfully. {$count} product(s) updated.{$doctorMsg}");
    }
}
