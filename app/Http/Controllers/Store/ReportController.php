<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StoreSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display store sales report - ONLY from store_sales table (actual customer sales)
     */
    public function sales(Request $request)
    {
        $storeId = auth()->id();

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Get actual sales to customers (NOT store purchases)
        $sales = StoreSale::where('store_id', $storeId)
            ->whereBetween('store_sales.created_at', [$startDate, $endDate])
            ->with('product')
            ->latest()
            ->get();

        // Total units sold
        $totalUnits = $sales->sum('quantity');

        // Total revenue from sales
        $totalRevenue = StoreSale::join('products', 'store_sales.product_id', '=', 'products.id')
            ->where('store_sales.store_id', $storeId)
            ->whereBetween('store_sales.created_at', [$startDate, $endDate])
            ->sum(DB::raw('store_sales.quantity * products.price'));

        return view('store.reports.sales', compact('sales', 'totalUnits', 'totalRevenue', 'startDate', 'endDate'));
    }
}
