<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $cacheKey = 'admin_dashboard_' . now()->format('Y-m-d_H');
        
        $dashboardData = Cache::remember($cacheKey, 600, function () {
            return [
                'stats' => [
                    'total_users' => User::count(),
                    'total_doctors' => User::role('Doctor')->count(),
                    'pending_doctors' => Doctor::pending()->count(),
                    'total_stores' => User::role('Store')->count(),
                    'total_products' => Product::count(),
                    'active_products' => Product::where('status', 'active')->count(),
                    'low_stock_products' => Product::where('status', 'active')->where('stock', '<', 10)->count(),
                    'total_orders' => Order::count(),
                    'pending_orders' => Order::where('status', 'pending')->count(),
                    'today_orders' => Order::whereDate('created_at', today())->count(),
                    'today_revenue' => Order::where('status', 'delivered')->whereDate('created_at', today())->sum('total_amount'),
                    'total_mrs' => User::role('MR')->count(),
                    'active_mrs' => User::role('MR')
                                            ->whereNotNull('last_login_at')
                                            ->whereDate('last_login_at', today())
                                            ->count(),
                ],
                'recent_orders' => Order::with('user')
                    ->latest()
                    ->limit(10)
                    ->get(),
            ];
        });

        return view('dashboard.admin', [
            'stats' => $dashboardData['stats'],
            'recent_orders' => $dashboardData['recent_orders'],
        ]);
    }
}
