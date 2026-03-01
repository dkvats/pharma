<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display doctor performance report using orders.doctor_id
     */
    public function performance(Request $request)
    {
        $doctor = auth()->user();

        /* DIRECT ORDERS */
        $directOrders = Order::where('user_id', $doctor->id)
            ->where('status', 'delivered');

        $directOrdersCount = $directOrders->count();
        $directOrdersValue = $directOrders->sum('total_amount');

        /* REFERRAL ORDERS (VERY IMPORTANT FIX) */
        $referralOrders = Order::where('doctor_id', $doctor->id)
            ->where('status', 'delivered');

        $referralOrdersCount = $referralOrders->count();
        $referralOrdersValue = $referralOrders->sum('total_amount');

        /* TOTAL CONTRIBUTION */
        $totalContribution = $directOrdersCount + $referralOrdersCount;

        /* TOTAL SALES (for chart scaling) */
        $totalSales = $directOrdersValue + $referralOrdersValue;

        /* RECENT ORDERS (BOTH TYPES) - With Products */
        $recentOrders = Order::where(function ($query) use ($doctor) {
                $query->where('user_id', $doctor->id)
                      ->orWhere('doctor_id', $doctor->id);
            })
            ->where('status', 'delivered')
            ->with(['items.product'])
            ->latest()
            ->take(10)
            ->get();

        return view('doctor.reports.performance', compact(
            'directOrdersCount',
            'directOrdersValue',
            'referralOrdersCount',
            'referralOrdersValue',
            'totalContribution',
            'totalSales',
            'recentOrders'
        ));
    }
}
