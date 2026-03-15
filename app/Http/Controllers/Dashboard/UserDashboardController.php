<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Offer;
use App\Models\RoleRequest;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Defensive check: Redirect Super Admin to their dashboard
        if (auth()->user()->hasRole('Super Admin')) {
            return redirect()->route('super-admin.dashboard');
        }

        $user = auth()->user();
        
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved_orders' => Order::where('user_id', $user->id)->where('status', 'approved')->count(),
            'completed_orders' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'total_spent' => Order::where('user_id', $user->id)->whereIn('status', ['approved', 'delivered'])->sum('total_amount'),
        ];

        $recent_orders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->latest()
            ->limit(5)
            ->get();

        // Orders with prescriptions
        $prescriptions_count = Order::where('user_id', $user->id)
            ->whereNotNull('prescription')
            ->count();

        // Fetch offers for dashboard (End User only)
        $dailyOffer = Offer::active()
            ->forUsers()
            ->where('offer_type', 'daily')
            ->latest()
            ->first();
        
        $ongoingOffers = Offer::active()
            ->forUsers()
            ->where('offer_type', 'ongoing')
            ->latest()
            ->take(3)
            ->get();
        
        // Role requests for this user
        $myRoleRequests = RoleRequest::where('user_id', $user->id)->latest()->get();
        $pendingRequest = $myRoleRequests->where('status', 'pending')->first();
        
        return view('dashboard.user', compact(
            'stats',
            'recent_orders',
            'prescriptions_count',
            'dailyOffer',
            'ongoingOffers',
            'myRoleRequests',
            'pendingRequest'
        ));
    }
}
