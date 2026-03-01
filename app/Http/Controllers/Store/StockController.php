<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSale;
use App\Models\StoreStock;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\StoreStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
}
