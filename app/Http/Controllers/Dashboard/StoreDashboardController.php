<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSale;
use App\Models\StoreStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreDashboardController extends Controller
{
    public function index()
    {
        $store = auth()->user();
        
        // Orders linked to this store via referral_code
        $linkedOrders = Order::where('store_id', $store->id);
        
        // Get stock and sales data
        $totalStockQuantity = StoreStock::where('store_id', $store->id)->sum('quantity');
        $totalSoldQuantity = StoreSale::where('store_id', $store->id)->sum('quantity');
        $availableStock = $totalStockQuantity - $totalSoldQuantity;

        // Get referred sales count (sales with doctor_id)
        $referredSalesCount = StoreSale::where('store_id', $store->id)
            ->whereNotNull('doctor_id')
            ->count();

        $stats = [
            'total_orders' => Order::where('user_id', $store->id)->count(),
            'pending_orders' => Order::where('user_id', $store->id)->where('status', 'pending')->count(),
            'completed_orders' => Order::where('user_id', $store->id)->where('status', 'delivered')->count(),
            'monthly_sales' => Order::where('user_id', $store->id)
                ->where('status', 'delivered')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            'linked_orders' => $linkedOrders->count(),
            'linked_sales' => $linkedOrders->where('status', 'delivered')->sum('total_amount'),
            'total_commission' => OrderItem::whereHas('order', function($q) use ($store) {
                $q->where('store_id', $store->id)->where('status', 'delivered');
            })->sum(DB::raw('commission * quantity')),
            // New stock stats
            'total_stock_quantity' => $totalStockQuantity,
            'total_sold_quantity' => $totalSoldQuantity,
            'available_stock' => $availableStock,
            'referred_sales_count' => $referredSalesCount,
        ];

        $recent_orders = Order::where('user_id', $store->id)
            ->with('items.product')
            ->latest()
            ->limit(5)
            ->get();

        $recent_linked_orders = Order::where('store_id', $store->id)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Recent store sales
        $recent_sales = StoreSale::where('store_id', $store->id)
            ->with(['product', 'doctor'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.store', compact('stats', 'recent_orders', 'recent_linked_orders', 'recent_sales'));
    }

    /**
     * Display store's referral doctors
     */
    public function referrals()
    {
        $store = auth()->user();
        
        // Get doctors who have referred orders to this store (via orders table which has total_amount)
        $referralDoctors = \App\Models\User::role('Doctor')
            ->whereHas('orders', function($q) use ($store) {
                $q->where('store_id', $store->id)->where('status', 'delivered');
            })
            ->withCount(['orders as total_sales' => function($q) use ($store) {
                $q->where('store_id', $store->id)->where('status', 'delivered');
            }])
            ->withSum(['orders as total_amount' => function($q) use ($store) {
                $q->where('store_id', $store->id)->where('status', 'delivered');
            }], 'total_amount')
            ->paginate(15);

        return view('store.referrals.index', compact('referralDoctors'));
    }
}
