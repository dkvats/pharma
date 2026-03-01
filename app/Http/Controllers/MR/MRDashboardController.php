<?php

namespace App\Http\Controllers\MR;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\DoctorVisit;
use App\Models\MR\Order;
use Illuminate\Http\Request;

class MRDashboardController extends Controller
{
    public function index()
    {
        $mrId = auth()->id();

        // Statistics
        $stats = [
            'doctors_covered_today' => Doctor::forMR($mrId)->visitedToday()->count(),
            'new_doctors_today' => Doctor::forMR($mrId)->newToday()->count(),
            'orders_today' => Order::forMR($mrId)->today()->count(),
            'total_doctors' => Doctor::forMR($mrId)->count(),
            'upcoming_visits' => DoctorVisit::forMR($mrId)->upcoming()->count(),
            'pending_orders' => Order::forMR($mrId)->byStatus('pending')->count(),
        ];

        // Recent visits
        $recentVisits = DoctorVisit::forMR($mrId)
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        // Recent orders
        $recentOrders = Order::forMR($mrId)
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        // Today's schedule
        $todayVisits = DoctorVisit::forMR($mrId)
            ->with('doctor')
            ->today()
            ->get();

        return view('mr.dashboard', compact(
            'stats',
            'recentVisits',
            'recentOrders',
            'todayVisits'
        ));
    }
}
