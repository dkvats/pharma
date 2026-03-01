<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SpinHistory;
use App\Services\DoctorTargetService;
use App\Services\DoctorTierService;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();

        // STEP 1 — DEBUG: Verify logged-in user
        // Uncomment below to debug, then comment after fixing
        /*
        dd([
            'auth_id' => auth()->id(),
            'auth_email' => $doctor->email,
            'auth_name' => $doctor->name,
            'unique_code' => $doctor->unique_code,
            'has_role_doctor' => $doctor->hasRole('Doctor'),
        ]);
        */

        /* DIRECT ORDERS - Doctor's own orders (all statuses) */
        $directOrdersCount = Order::where('user_id', $doctor->id)
            ->where('doctor_id', $doctor->id)
            ->count();

        /* REFERRAL ORDERS - Orders from others using doctor's code (all statuses) */
        $referralOrdersCount = Order::where('doctor_id', $doctor->id)
            ->where('user_id', '!=', $doctor->id)  // Exclude doctor's own orders
            ->count();

        /* DELIVERED ORDERS (for contribution calculation) */
        $deliveredDirectCount = Order::where('user_id', $doctor->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'delivered')
            ->count();
        
        $deliveredReferralCount = Order::where('doctor_id', $doctor->id)
            ->where('user_id', '!=', $doctor->id)
            ->where('status', 'delivered')
            ->count();

        /* TOTAL CONTRIBUTION - Delivered orders only */
        $totalContribution = $deliveredDirectCount + $deliveredReferralCount;

        /* TOTAL ORDERS */
        $totalOrders = $directOrdersCount + $referralOrdersCount;

        /* PENDING ORDERS */
        $pendingOrders = Order::where(function ($q) use ($doctor) {
                $q->where('user_id', $doctor->id)
                  ->orWhere('doctor_id', $doctor->id);
            })
            ->where('status', 'pending')
            ->count();

        // Stats array for blade
        $stats = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'direct_orders' => $directOrdersCount,
            'referral_orders' => $referralOrdersCount,
            'total_contribution' => $totalContribution,
        ];

        // Get target progress
        $targetService = app(DoctorTargetService::class);
        $targetProgress = $targetService->getProgress($doctor->id);

        // Spin history
        $spinStats = [
            'total_spins' => SpinHistory::where('doctor_id', $doctor->id)->count(),
            'claimed_rewards' => SpinHistory::where('doctor_id', $doctor->id)->whereNotNull('claimed_at')->count(),
            'pending_claims' => SpinHistory::where('doctor_id', $doctor->id)->whereNull('claimed_at')->whereNotNull('reward_id')->count(),
        ];

        // Recent orders (both direct and referral) - limit 10
        // Include pending, approved, and delivered orders
        $recent_orders = Order::where(function ($q) use ($doctor) {
                $q->where('user_id', $doctor->id)
                  ->orWhere('doctor_id', $doctor->id);
            })
            ->with('items.product')
            ->latest()
            ->limit(10)
            ->get();

        // Get tier and rank info
        $tierService = app(DoctorTierService::class);
        $tier = $tierService->getTier($doctor->id);

        // Get monthly product count (SUM of quantities, not count of orders)
        $monthlyCount = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctor->id)
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->sum('order_items.quantity') ?? 0;

        // Calculate rank based on product quantity (not order count)
        $monthlyRank = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select('orders.doctor_id')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('orders.doctor_id')
            ->havingRaw('SUM(order_items.quantity) > ?', [$monthlyCount])
            ->count() + 1;

        $rankInfo = [
            'tier' => $tier,
            'monthly_rank' => $monthlyRank,
            'monthly_sales' => $monthlyCount,
        ];

        return view('dashboard.doctor', compact('stats', 'targetProgress', 'spinStats', 'recent_orders', 'rankInfo'));
    }

    /**
     * Export doctor orders to CSV
     */
    public function exportOrders()
    {
        $doctor = auth()->user();
        
        $orders = Order::where(function ($q) use ($doctor) {
                $q->where('user_id', $doctor->id)
                  ->orWhere('doctor_id', $doctor->id);
            })
            ->with(['items.product', 'user', 'store'])
            ->latest()
            ->get();

        $filename = 'doctor_orders_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($orders, $doctor) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['Order Number', 'Date', 'Type', 'Customer/Store', 'Products', 'Amount', 'Status']);
            
            foreach ($orders as $order) {
                $type = $order->user_id == $doctor->id ? 'Direct' : 'Referral';
                $customer = $order->store ? $order->store->name : ($order->user ? $order->user->name : 'N/A');
                $products = $order->items->map(fn($item) => $item->product->name ?? 'N/A')->join('; ');
                
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $type,
                    $customer,
                    $products,
                    $order->total_amount,
                    $order->status,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display doctor's referral orders
     */
    public function referrals()
    {
        $doctor = auth()->user();

        $orders = Order::where('doctor_id', $doctor->id)
            ->where('user_id', '!=', $doctor->id)  // Exclude doctor's own orders
            ->where('status', 'delivered')
            ->with(['user', 'items.product'])
            ->latest()
            ->paginate(20);

        return view('doctor.referrals.index', compact('orders'));
    }
}
