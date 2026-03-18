<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserOfferUsage;
use App\Services\ActivityLogService;
use App\Services\DoctorTargetService;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\SaleClassificationService;
use App\Services\SystemSettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $saleClassificationService;
    protected $doctorTargetService;

    public function __construct(
        SaleClassificationService $saleClassificationService,
        DoctorTargetService $doctorTargetService
    ) {
        $this->saleClassificationService = $saleClassificationService;
        $this->doctorTargetService = $doctorTargetService;
    }

    /**
     * Show order creation form
     */
    public function create(Request $request)
    {
        $fromCart = $request->has('from_cart');
        
        // If coming from cart, load cart items
        $cartItems = null;
        if ($fromCart) {
            $cart = auth()->user()->cart;
            if ($cart && $cart->items->isNotEmpty()) {
                $cartItems = $cart->items->load('product');
            }
        }

        $products = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->get();

        // Get doctors and stores for referral
        $doctors = User::role('Doctor')->where('status', 'active')->get();
        $stores = User::role('Store')->where('status', 'active')->get();

        // Get active offers for discount display (role-based)
        $user = auth()->user();
        $dailyOffer = null;
        $ongoingOffers = collect();
        
        if ($user->hasRole('End User')) {
            $dailyOffer = Offer::active()->forUsers()->where('offer_type', 'daily')->first();
            $ongoingOffers = Offer::active()->forUsers()->where('offer_type', 'ongoing')->get();
        } elseif ($user->hasRole('Store')) {
            $dailyOffer = Offer::active()->forStores()->where('offer_type', 'daily')->first();
            $ongoingOffers = Offer::active()->forStores()->where('offer_type', 'ongoing')->get();
        }

        return view('orders.create', compact('products', 'doctors', 'stores', 'cartItems', 'fromCart', 'dailyOffer', 'ongoingOffers'));
    }

    /**
     * Store a new order
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Handle cart checkout - populate items from cart
        if ($request->has('from_cart')) {
            $cart = $user->cart;
            
            if (!$cart || $cart->items->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
            }
            
            $cartItems = $cart->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ];
            })->toArray();
            
            $request->merge(['items' => $cartItems]);
        }
        
        // Only Store or End User can upload prescriptions
        // Doctors are exempt from prescription upload requirement
        $canUploadPrescription = $user->hasRole('Store') || $user->hasRole('End User');
        $isDoctor = $user->hasRole('Doctor');
        
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'referral_code' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'prescription' => [$canUploadPrescription || $isDoctor ? 'nullable' : 'prohibited', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        // Pre-validate stock before transaction to provide graceful error messages
        $productIds = collect($validated['items'])->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        foreach ($validated['items'] as $item) {
            $product = $products->get($item['product_id']);
            if ($product && $product->stock < $item['quantity']) {
                return back()->withErrors([
                    'quantity' => "Insufficient stock for {$product->name}. Only {$product->stock} units available."
                ])->withInput();
            }
        }

        return DB::transaction(function () use ($request, $validated, $user, $canUploadPrescription) {
            $user = auth()->user();
            $totalCommission = 0;

            // CRITICAL: Sort product IDs to prevent deadlock (consistent locking order)
            $productIds = collect($validated['items'])
                ->pluck('product_id')
                ->sort()
                ->values()
                ->toArray();
            
            // Lock ALL products at once in sorted order (deadlock prevention)
            $lockedProducts = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            
            // Calculate totals and validate stock + prescription requirements
            $prescriptionRequired = false;
            $subtotal = 0; // Track subtotal before discount
            
            foreach ($validated['items'] as $item) {
                $productId = $item['product_id'];
                
                // Verify product was locked (should always exist due to validation)
                if (!isset($lockedProducts[$productId])) {
                    throw new \Exception("Product not found or not locked: {$productId}");
                }
                
                $product = $lockedProducts[$productId];
                
                // Validate stock AFTER locking (prevents overselling - safety check)
                // This should never fail if pre-validation passed, but protects against race conditions
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock changed during order processing for {$product->name}. Please try again.");
                }
                
                // Check if any product requires prescription (LOCKED at order creation time)
                if ($product->requires_prescription) {
                    $prescriptionRequired = true;
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $commission = $product->commission * $item['quantity'];
                $subtotal += $itemSubtotal;
                $totalCommission += $commission;
            }

            // Apply offer discount if selected (role-based, two separate if blocks)
            $discountAmount = 0;
            $offerId = null;
            $appliedOffer = null;

            $requestedOfferId = $validated['offer_id'] ?? null;

            if ($requestedOfferId) {
                // End User: find from user offers
                if ($user->hasRole('End User')) {
                    $appliedOffer = Offer::active()->forUsers()->find($requestedOfferId);
                }

                // Store: find from store offers
                if ($user->hasRole('Store')) {
                    $appliedOffer = Offer::active()->forStores()->find($requestedOfferId);
                }

                if ($appliedOffer && $appliedOffer->isValid()) {
                    if ($appliedOffer->discount_type === 'percentage') {
                        $discountAmount = $subtotal * ($appliedOffer->discount_value / 100);
                    } else {
                        $discountAmount = $appliedOffer->discount_value;
                    }
                    // Cap discount at subtotal
                    $discountAmount = min($discountAmount, $subtotal);
                    $offerId = $appliedOffer->id;
                }
            }

            // Calculate final total ONCE — never reassigned after this
            $totalAmount = $subtotal - $discountAmount;
            
            // PRESCRIPTION VALIDATION DISABLED - Users can purchase without prescription
            // Prescription upload is now optional for all users
            // Doctors and Stores remain exempt (as before)
            $isDoctor = $user->hasRole('Doctor');
            $isStore = $user->hasRole('Store');
            
            // Prescription enforcement disabled - only validate file if provided
            // if ($prescriptionRequired && !$request->hasFile('prescription') && !$isDoctor && !$isStore) {
            //     return back()->with('error', 'One or more products require a prescription. Please upload a prescription file.')->withInput();
            // }

            // Validate referral code if provided
            $doctorId = null;
            $storeId = null;
            $referralCode = $validated['referral_code'] ?? null;

            if ($referralCode) {
                $referrer = $this->saleClassificationService->validateReferralCode($referralCode);
                if (!$referrer) {
                    return back()->with('error', 'Invalid referral code')->withInput();
                }

                // ENTERPRISE: Prevent self-referral
                if ($user->id === $referrer->id) {
                    ActivityLogService::log(
                        'self_referral_attempt',
                        $user,
                        "User {$user->id} attempted to use their own referral code: {$referralCode}"
                    );
                    return back()->with('error', 'Self-referral is not allowed.')->withInput();
                }

                if ($referrer->hasRole('Doctor')) {
                    $doctorId = $referrer->id;
                } elseif ($referrer->hasRole('Store')) {
                    $storeId = $referrer->id;
                }
            }

            // If doctor is placing order, set doctor_id
            if ($user->hasRole('Doctor')) {
                $doctorId = $user->id;
            }

            // If store is placing order, set store_id
            if ($user->hasRole('Store')) {
                $storeId = $user->id;
            }

            // If MR is placing order and selected a doctor, set doctor_id
            if ($user->hasRole('MR') && $request->filled('doctor_id')) {
                $doctorId = $request->input('doctor_id');
            }

            // Handle prescription upload (only Store or End User can upload)
            // Stored in PRIVATE disk for security - NOT publicly accessible
            $prescriptionPath = null;
            if ($canUploadPrescription && $request->hasFile('prescription')) {
                $prescriptionPath = $request->file('prescription')->store('prescriptions', 'private');
            }

            // Determine sale type BEFORE creating order (for immutability)
            $saleType = $this->determineSaleType($user, $doctorId, $storeId, $referralCode);

            // Check for auto-approve setting from CMS
            $initialStatus = 'pending';
            $autoApprove = SystemSettingService::get('order_auto_approve', false);
            
            // Auto-approve if enabled AND no prescription required
            // Orders with prescriptions still need manual review
            if ($autoApprove && !$prescriptionRequired) {
                $initialStatus = 'approved';
            }

            // Create order with LOCKED prescription requirement and IMMUTABLE sale_type
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'doctor_id' => $doctorId,
                'store_id' => $storeId,
                'referral_code' => $referralCode,
                'status' => $initialStatus,
                'sale_type' => $saleType, // Set at creation - immutable
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'offer_id' => $offerId ?? null,
                'notes' => $validated['notes'] ?? null,
                'prescription' => $prescriptionPath,
                'prescription_uploaded_at' => $prescriptionPath ? now() : null,
                'prescription_required' => $prescriptionRequired, // LOCKED at creation time
            ]);

            // Create order items and update stock using LOCKED products
            foreach ($validated['items'] as $item) {
                // Use the already-locked product instance
                $product = $lockedProducts[$item['product_id']];                
                $qty          = $item['quantity'];
                $itemPrice    = $product->price;
                $gstPercent   = (float) ($product->gst_percent ?? 0);
                $gstAmount    = round($itemPrice * $qty * $gstPercent / 100, 2);
                $totalWithGst = round($itemPrice * $qty + $gstAmount, 2);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'quantity'      => $qty,
                    'price'         => $itemPrice,
                    'gst_percent'   => $gstPercent,
                    'gst_amount'    => $gstAmount,
                    'total_with_gst'=> $totalWithGst,
                    'commission'    => $product->commission,
                    'subtotal'      => $itemPrice * $qty,
                ]);

                // FEFO batch deduction: deduct from nearest-expiry batch first.
                // Falls back to direct stock decrement if no batches configured.
                $hasBatches = $product->batches()
                    ->whereDate('expiry_date', '>=', now())
                    ->where('quantity', '>', 0)
                    ->exists();

                if ($hasBatches) {
                    $product->deductBatchStockFefo($qty);
                    // syncStockFromBatches() is called inside deductBatchStockFefo()
                } else {
                    // Legacy fallback: decrement products.stock directly
                    $product->decrement('stock', $qty);
                }
            }

            // Note: Target will be updated when order is approved by admin
            // Not on order placement

            // Log order placement
            ActivityLogService::logOrderPlaced($order);

            // Log to new activity log system
            logActivity(
                'Order Created',
                'Order',
                $order->id,
                "Order #{$order->order_number} created by {$user->name}"
            );

            // Record offer usage if offer was applied (for End Users)
            if ($offerId && $discountAmount > 0 && $user->hasRole('End User')) {
                UserOfferUsage::recordUsage(
                    $user->id,
                    $offerId,
                    $order->id,
                    $discountAmount
                );
            }

            // Clear cart if order was created from cart
            if ($request->has('from_cart')) {
                $cart = $user->cart;
                if ($cart) {
                    $cart->items()->delete();
                }
            }

            // Send order confirmation notification
            NotificationService::sendOrderConfirmation($order, $user);

            // Generate invoice automatically
            $invoiceService = new InvoiceService();
            $invoice = $invoiceService->generateFromOrder($order);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Order #' . $order->order_number . '. Invoice #' . $invoice->invoice_number . ' generated.');
        });
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $user = auth()->user();
        
        // Authorization logic based on role
        $canView = false;
        
        if ($user->hasRole('Admin')) {
            $canView = true;
        } elseif ($user->hasRole('Doctor')) {
            // Doctor can see: direct orders, referral orders
            $canView = ($order->doctor_id === $user->id) || 
                       ($order->referral_code && $order->doctor && $order->doctor->id === $user->id);
        } elseif ($user->hasRole('Store')) {
            // Store can see: store linked orders
            $canView = ($order->store_id === $user->id) ||
                       ($order->sale_type === 'store_linked' && $order->store_id === $user->id);
        } else {
            // End User can see: own orders only
            $canView = $order->user_id === $user->id;
        }
        
        if (!$canView) {
            abort(403, 'Unauthorized access. You cannot view this order.');
        }

        $order->load(['items.product', 'user', 'doctor', 'store']);
        return view('orders.show', compact('order'));
    }

    /**
     * Display user's order history based on role
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = Order::query();
        
        if ($user->hasRole('Doctor')) {
            // Doctor sees: direct orders + referral orders
            $query->where(function ($q) use ($user) {
                $q->where('doctor_id', $user->id)
                  ->orWhere(function ($sq) use ($user) {
                      $sq->whereNotNull('referral_code')
                         ->whereHas('doctor', function ($dq) use ($user) {
                             $dq->where('id', $user->id);
                         });
                  });
            });
        } elseif ($user->hasRole('Store')) {
            // Store sees: store linked orders
            $query->where('store_id', $user->id);
        } else {
            // End User sees: own orders only
            $query->where('user_id', $user->id);
        }
        
        $orders = $query->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Determine sale type before order creation (for immutability)
     */
    private function determineSaleType($user, $doctorId, $storeId, $referralCode): string
    {
        // Doctor ordering for themselves
        if ($doctorId && $user->id === $doctorId) {
            return 'doctor_direct';
        }

        // Has referral code
        if ($referralCode) {
            return 'referral';
        }

        // Store placing order
        if ($storeId) {
            return 'store_linked';
        }

        // Default: Company Direct Sale (End User direct order)
        return 'company_direct';
    }

    /**
     * Cancel an order (End User only, pending orders only)
     */
    public function cancel(Order $order)
    {
        $user = auth()->user();

        // Only End User can cancel their own orders
        if (!$user->hasRole('End User')) {
            abort(403, 'Only End Users can cancel orders.');
        }

        // Verify order belongs to user
        if ($order->user_id !== $user->id) {
            abort(403, 'You can only cancel your own orders.');
        }

        // Only pending orders can be cancelled
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled. This order is already ' . $order->status . '.');
        }

        // Restore stock for order items
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        // Update order status
        $order->status = 'cancelled';
        $order->save();

        // Log cancellation
        ActivityLogService::log(
            'order_cancelled',
            $user,
            "Order #{$order->order_number} cancelled by user {$user->name}"
        );

        logActivity(
            'Order Cancelled',
            'Order',
            $order->id,
            "Order #{$order->order_number} cancelled by {$user->name}"
        );

        return back()->with('success', 'Order cancelled successfully. Stock has been restored.');
    }
}
