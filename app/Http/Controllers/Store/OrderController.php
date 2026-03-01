<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        return view('store.orders.show', compact('order'));
    }
}
