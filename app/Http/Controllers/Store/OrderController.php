<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreCancellationRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display list of orders for the store
     */
    public function index()
    {
        $storeId = auth()->id();

        $orders = Order::where('user_id', $storeId)
            ->orWhere('store_id', $storeId)
            ->latest()
            ->paginate(15);

        return view('store.orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        // Security: ensure this store owns the order
        if ($order->user_id != auth()->id() && $order->store_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load('items.product');
        
        // Check if there's a pending cancellation request for approved orders
        $pendingRequest = null;
        if ($order->status === 'approved') {
            $pendingRequest = StoreCancellationRequest::where('order_id', $order->id)
                ->where('store_id', auth()->id())
                ->where('status', 'pending')
                ->first();
        }

        return view('store.orders.show', compact('order', 'pendingRequest'));
    }

    /**
     * Cancel a pending order (Store only)
     */
    public function cancel(Order $order)
    {
        // Security: ensure this store owns the order
        if ($order->user_id != auth()->id() && $order->store_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Only pending orders can be cancelled directly
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled directly.');
        }

        // Update order status
        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        // Restore stock for order items
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        // Log cancellation
        ActivityLogService::log(
            'store_order_cancelled',
            auth()->user(),
            "Store order #{$order->order_number} cancelled by store"
        );

        return back()->with('success', 'Order cancelled successfully. Stock has been restored.');
    }

    /**
     * Show form to request cancellation for approved order
     */
    public function requestCancelForm(Order $order)
    {
        // Security: ensure this store owns the order
        if ($order->user_id != auth()->id() && $order->store_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Only approved orders can have cancellation requests
        if ($order->status !== 'approved') {
            return back()->with('error', 'Cancellation requests can only be submitted for approved orders.');
        }

        // Check if request already exists
        $existingRequest = StoreCancellationRequest::where('order_id', $order->id)
            ->where('store_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('info', 'You already have a pending cancellation request for this order.');
        }

        return view('store.orders.request-cancel', compact('order'));
    }

    /**
     * Submit cancellation request for approved order
     */
    public function submitCancelRequest(Request $request, Order $order)
    {
        // Security: ensure this store owns the order
        if ($order->user_id != auth()->id() && $order->store_id != auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        // Only approved orders can have cancellation requests
        if ($order->status !== 'approved') {
            return back()->with('error', 'Cancellation requests can only be submitted for approved orders.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Check if request already exists
        $existingRequest = StoreCancellationRequest::where('order_id', $order->id)
            ->where('store_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('info', 'You already have a pending cancellation request for this order.');
        }

        // Create cancellation request
        StoreCancellationRequest::create([
            'order_id' => $order->id,
            'store_id' => auth()->id(),
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Log request
        ActivityLogService::log(
            'store_cancellation_requested',
            auth()->user(),
            "Cancellation request submitted for order #{$order->order_number}"
        );

        return redirect()->route('store.orders.show', $order)
            ->with('success', 'Cancellation request submitted to Admin for review.');
    }
}
