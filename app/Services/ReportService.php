<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales summary for a date range
     */
    public function getSalesSummary($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $orders = Order::whereBetween('created_at', [$startDate, $endDate]);

        // Calculate total commission based on different sale types
        // Doctor Direct: 5% commission
        // Referral: 3% commission
        // Store Linked: 2% commission
        // Company Direct: 0% commission
        $totalCommission = $orders->get()->sum(function ($order) {
            $rate = match($order->sale_type) {
                'doctor_direct' => 0.05,
                'referral' => 0.03,
                'store_linked' => 0.02,
                'company_direct' => 0,
                default => 0,
            };
            return $order->total_amount * $rate;
        });

        return [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_commission' => $totalCommission,
            'average_order_value' => $orders->avg('total_amount') ?? 0,
            'pending_orders' => (clone $orders)->where('status', 'pending')->count(),
            'approved_orders' => (clone $orders)->where('status', 'approved')->count(),
            'delivered_orders' => (clone $orders)->where('status', 'delivered')->count(),
            'rejected_orders' => (clone $orders)->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Get sales by sale type
     */
    public function getSalesByType($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('sale_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('sale_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->sale_type => [
                    'count' => $item->count,
                    'total' => $item->total,
                ]];
            });
    }

    /**
     * Get top selling products
     */
    public function getTopProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                'products.category',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.category')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get doctor performance report
     */
    public function getDoctorPerformance($doctorId = null, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        // Get orders linked to doctors
        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('doctor_id');

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $orderPerformance = $query->select(
            'doctor_id',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('SUM(CASE WHEN sale_type = "doctor_direct" THEN total_amount ELSE 0 END) as direct_sales'),
            DB::raw('SUM(CASE WHEN sale_type = "referral" THEN total_amount ELSE 0 END) as referral_sales')
        )
        ->groupBy('doctor_id')
        ->with('doctor:id,name,code')
        ->get();

        // Get store sales referred by doctors
        $storeSalesQuery = StoreSale::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('doctor_id');

        if ($doctorId) {
            $storeSalesQuery->where('doctor_id', $doctorId);
        }

        $storeSalesPerformance = $storeSalesQuery->select(
            'doctor_id',
            DB::raw('COUNT(*) as referred_store_sales_count'),
            DB::raw('SUM(quantity) as referred_store_sales_quantity')
        )
        ->groupBy('doctor_id')
        ->get()
        ->keyBy('doctor_id');

        // Merge order performance with store sales performance
        return $orderPerformance->map(function ($doctor) use ($storeSalesPerformance) {
            $storeSales = $storeSalesPerformance->get($doctor->doctor_id);
            $doctor->referred_store_sales_count = $storeSales ? $storeSales->referred_store_sales_count : 0;
            $doctor->referred_store_sales_quantity = $storeSales ? $storeSales->referred_store_sales_quantity : 0;
            $doctor->total_units = $doctor->total_orders + $doctor->referred_store_sales_quantity;
            return $doctor;
        });
    }

    /**
     * Get store performance report
     */
    public function getStorePerformance($storeId = null, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('store_id');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        return $query->select(
            'store_id',
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_sales')
        )
        ->groupBy('store_id')
        ->with('store:id,name,code')
        ->get();
    }

    /**
     * Get monthly sales trend
     */
    public function getMonthlyTrend($months = 6)
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $orders = Order::whereBetween('created_at', [$start, $end]);

            $data[] = [
                'month' => $date->format('M Y'),
                'orders' => $orders->count(),
                'revenue' => $orders->sum('total_amount'),
            ];
        }

        return $data;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts($threshold = 10)
    {
        return Product::where('stock', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Get dashboard stats
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
            'month_revenue' => Order::where('created_at', '>=', $thisMonth)->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_count' => Product::where('stock', '<=', 10)->where('status', 'active')->count(),
        ];
    }
}
