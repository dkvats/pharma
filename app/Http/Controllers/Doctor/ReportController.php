<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSale;
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

    /**
     * Display referral sales — products sold by stores via doctor's prescription.
     * SECURITY: Only returns records where doctor_id = authenticated doctor.
     * PRIVACY:  Selects ONLY product name, quantity, store name, and date.
     *           Price / subtotal / commission columns are intentionally excluded.
     */
    public function referralSales(Request $request)
    {
        $doctorId = auth()->id();

        $sales = StoreSale::with(['product:id,name', 'store:id,name'])
            ->where('doctor_id', $doctorId)
            ->latest()
            ->paginate(20);

        // Summary counts (no financial data)
        $totalSales   = StoreSale::where('doctor_id', $doctorId)->count();
        $totalQty     = StoreSale::where('doctor_id', $doctorId)->sum('quantity');
        $thisMonthQty = StoreSale::where('doctor_id', $doctorId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');

        return view('doctor.reports.referral-sales', compact(
            'sales',
            'totalSales',
            'totalQty',
            'thisMonthQty'
        ));
    }
}
