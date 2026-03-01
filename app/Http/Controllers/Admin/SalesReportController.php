<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    /**
     * Display sales by doctor and store report
     */
    public function index(Request $request)
    {
        // Date range filters
        $startDate = $request->filled('start_date') ? $request->start_date : now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->filled('end_date') ? $request->end_date : now()->format('Y-m-d');

        // Sales by Doctor
        $doctorSales = Order::whereNotNull('doctor_id')
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('doctor_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_amount'))
            ->groupBy('doctor_id')
            ->with('doctor:id,name,unique_code')
            ->orderByDesc('total_orders')
            ->get();

        // Sales by Store
        $storeSales = Order::whereNotNull('store_id')
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('store_id', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(total_amount) as total_amount'))
            ->groupBy('store_id')
            ->with('store:id,name,city')
            ->orderByDesc('total_orders')
            ->get();

        // Summary stats
        $summary = [
            'total_doctor_orders' => $doctorSales->sum('total_orders'),
            'total_doctor_amount' => $doctorSales->sum('total_amount'),
            'total_store_orders' => $storeSales->sum('total_orders'),
            'total_store_amount' => $storeSales->sum('total_amount'),
            'unique_doctors' => $doctorSales->count(),
            'unique_stores' => $storeSales->count(),
        ];

        return view('admin.reports.sales-by-entity', compact('doctorSales', 'storeSales', 'summary', 'startDate', 'endDate'));
    }
}
