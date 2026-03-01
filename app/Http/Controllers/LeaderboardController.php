<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\DoctorTierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    protected $tierService;

    public function __construct(DoctorTierService $tierService)
    {
        $this->tierService = $tierService;
    }

    /**
     * Display monthly leaderboard
     */
    public function monthly(Request $request)
    {
        $cacheKey = 'leaderboard_monthly_' . now()->format('Y_m');
        $page = $request->get('page', 1);
        $perPage = 50;
        
        // Get cached leaderboard data (full sorted list)
        $leaderboardData = Cache::remember($cacheKey, 300, function () {
            return Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->select('orders.doctor_id', DB::raw('SUM(order_items.quantity) as total_products'))
                ->where('orders.status', 'delivered')
                ->whereNotNull('orders.doctor_id')
                ->whereMonth('orders.created_at', now()->month)
                ->whereYear('orders.created_at', now()->year)
                ->groupBy('orders.doctor_id')
                ->orderByDesc('total_products')
                ->get();
        });

        // Manual pagination on cached collection
        $total = $leaderboardData->count();
        $items = $leaderboardData->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Create paginator
        $rankings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Load doctor relationships
        $doctorIds = $rankings->pluck('doctor_id')->toArray();
        $doctors = User::whereIn('id', $doctorIds)->get(['id', 'name', 'unique_code'])->keyBy('id');
        $tiers = $this->tierService->getTiersForDoctors($doctorIds);

        // Add rank, tier and doctor to each item
        $startRank = ($rankings->currentPage() - 1) * $rankings->perPage() + 1;
        foreach ($rankings as $index => $ranking) {
            $ranking->rank = $startRank + $index;
            $ranking->tier = $tiers[$ranking->doctor_id] ?? null;
            $ranking->doctor = $doctors[$ranking->doctor_id] ?? null;
        }

        // Get current doctor ID if authenticated
        $currentDoctorId = auth()->user()?->hasRole('Doctor') ? auth()->id() : null;

        return view('leaderboard.monthly', compact('rankings', 'currentDoctorId'));
    }

    /**
     * Display all-time leaderboard
     */
    public function allTime(Request $request)
    {
        $cacheKey = 'leaderboard_all_time';
        $page = $request->get('page', 1);
        $perPage = 50;
        
        // Get cached leaderboard data (full sorted list)
        $leaderboardData = Cache::remember($cacheKey, 600, function () {
            return Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->select('orders.doctor_id', DB::raw('SUM(order_items.quantity) as total_products'))
                ->where('orders.status', 'delivered')
                ->whereNotNull('orders.doctor_id')
                ->groupBy('orders.doctor_id')
                ->orderByDesc('total_products')
                ->get();
        });

        // Manual pagination on cached collection
        $total = $leaderboardData->count();
        $items = $leaderboardData->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Create paginator
        $rankings = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Load doctor relationships
        $doctorIds = $rankings->pluck('doctor_id')->toArray();
        $doctors = User::whereIn('id', $doctorIds)->get(['id', 'name', 'unique_code'])->keyBy('id');
        $tiers = $this->tierService->getTiersForDoctors($doctorIds);

        // Add rank, tier and doctor to each item
        $startRank = ($rankings->currentPage() - 1) * $rankings->perPage() + 1;
        foreach ($rankings as $index => $ranking) {
            $ranking->rank = $startRank + $index;
            $ranking->tier = $tiers[$ranking->doctor_id] ?? null;
            $ranking->doctor = $doctors[$ranking->doctor_id] ?? null;
        }

        // Get current doctor ID if authenticated
        $currentDoctorId = auth()->user()?->hasRole('Doctor') ? auth()->id() : null;

        return view('leaderboard.all-time', compact('rankings', 'currentDoctorId'));
    }

    /**
     * Get doctor's current rank and stats (for dashboard)
     *
     * @param int $doctorId
     * @return array
     */
    public function getDoctorRank(int $doctorId): array
    {
        // Get tier info
        $tier = $this->tierService->getTier($doctorId);

        // Get monthly product count
        $monthlyProductCount = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.doctor_id', $doctorId)
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->sum('order_items.quantity') ?? 0;

        // Get monthly rank based on product count
        $monthlyRank = Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select('orders.doctor_id')
            ->where('orders.status', 'delivered')
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->groupBy('orders.doctor_id')
            ->havingRaw('SUM(order_items.quantity) > ?', [$monthlyProductCount])
            ->count() + 1;

        return [
            'tier' => $tier,
            'monthly_rank' => $monthlyRank,
            'monthly_sales' => $monthlyProductCount,
        ];
    }
}
