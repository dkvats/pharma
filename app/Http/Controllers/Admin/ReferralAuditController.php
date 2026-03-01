<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReferralAuditController extends Controller
{
    /**
     * Display referral audit page
     */
    public function index(Request $request)
    {
        $query = Order::whereNotNull('doctor_id')
            ->with(['doctor', 'store', 'user'])
            ->latest();

        // Filter by doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->paginate(20);

        // Stats
        $stats = [
            'total_referrals' => Order::whereNotNull('doctor_id')->count(),
            'total_amount' => Order::whereNotNull('doctor_id')->sum('total_amount'),
            'unique_doctors' => Order::whereNotNull('doctor_id')->distinct('doctor_id')->count('doctor_id'),
            'unique_stores' => Order::whereNotNull('doctor_id')->distinct('store_id')->count('store_id'),
        ];

        return view('admin.referrals.audit', compact('orders', 'stats'));
    }
}
