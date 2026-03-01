<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\DoctorTargetService;
use App\Services\SaleClassificationService;
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

        // Get active offers for discount display
        $dailyOffer = Offer::active()->where('offer_type', 'daily')->first();
        $ongoingOffers = Offer::active()->where('offer_type', 'ongoing')->get();

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
            $totalAmount = 0;
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

                $subtotal = $product->price * $item['quantity'];
                $commission = $product->commission * $item['quantity'];
                $totalAmount += $subtotal;
                $totalCommission += $commission;
            }

            // Apply offer discount if selected
            $discountAmount = 0;
            $offerId = $request->input('offer_id');
            if ($offerId) {
                $offer = Offer::active()->find($offerId);
                if ($offer) {
                    if ($offer->discount_type === 'percentage') {
                        $discountAmount = $totalAmount * ($offer->discount_value / 100);
                    } else {
                        $discountAmount = $offer->discount_value;
                    }
                    // Cap discount at total amount
                    $discountAmount = min($discountAmount, $totalAmount);
                    $totalAmount -= $discountAmount;
                }
            }
            
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

            // Create order with LOCKED prescription requirement and IMMUTABLE sale_type
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'doctor_id' => $doctorId,
                'store_id' => $storeId,
                'referral_code' => $referralCode,
                'status' => 'pending',
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
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'commission' => $product->commission,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                // Decrement stock using locked product (guaranteed consistent)
                $product->decrement('stock', $item['quantity']);
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

            // Clear cart if order was created from cart
            if ($request->has('from_cart')) {
                $cart = $user->cart;
                if ($cart) {
                    $cart->items()->delete();
                }
            }

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully! Order #' . $order->order_number);
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
}
