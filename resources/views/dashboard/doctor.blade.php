@extends('layouts.app')

@section('title', 'Doctor Dashboard')
@section('page-title', 'Doctor Dashboard')
@section('page-description', 'Welcome back, Dr. ' . auth()->user()->name)

@section('content')
@php
$user = auth()->user();
$doctor = $user->doctor;
$currentMonth = now()->format('Y-m');

// Get target progress from service (product-based)
$targetService = app(\App\Services\DoctorTargetService::class);
$targetProgress = $targetService->getProgress($user->id);

// Calculate stats - Direct sales (doctor's own orders)
$directSales = \App\Models\Order::where('user_id', $user->id)
    ->where('doctor_id', $user->id)
    ->where('status', 'delivered')
    ->whereMonth('created_at', now()->month)
    ->sum('total_amount');

// Referral sales (orders from others using doctor's code) - exclude self
$referralSales = \App\Models\Order::where('doctor_id', $user->id)
    ->where('user_id', '!=', $user->id)
    ->where('status', 'delivered')
    ->whereMonth('created_at', now()->month)
    ->sum('total_amount');

$totalSales = $directSales + $referralSales;

// Product-based target progress
$currentProducts = $targetProgress['current'] ?? 0;
$targetProducts = $targetProgress['target'] ?? 30;
$progress = $targetProgress['percentage'] ?? 0;
$remainingSpins = $targetProgress['remaining_spins'] ?? 0;

// Recent orders
$recentOrders = $user->orders()->latest()->take(5)->get();
$referralOrders = \App\Models\Order::where('doctor_id', $user->id)
    ->where('user_id', '!=', $user->id)
    ->latest()
    ->take(5)
    ->get();

// Spin eligibility with safe fallback
$spinService = app(\App\Services\SpinService::class);
$canSpin = method_exists($spinService, 'canSpin') 
    ? $spinService->canSpin($user->id) 
    : ($remainingSpins > 0);

// Rank info — computed here as safety net in case controller variable is shadowed
if (!isset($rankInfo)) {
    $rMonthlyCount = (int) (\App\Models\Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->where('orders.doctor_id', $user->id)
        ->where('orders.status', 'delivered')
        ->whereMonth('orders.created_at', now()->month)
        ->whereYear('orders.created_at', now()->year)
        ->sum('order_items.quantity') ?? 0);

    $rMonthlyRank = \App\Models\Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->select('orders.doctor_id')
        ->where('orders.status', 'delivered')
        ->whereNotNull('orders.doctor_id')
        ->whereMonth('orders.created_at', now()->month)
        ->whereYear('orders.created_at', now()->year)
        ->groupBy('orders.doctor_id')
        ->havingRaw('SUM(order_items.quantity) > ?', [$rMonthlyCount])
        ->count() + 1;

    $rTotalDoctors = \App\Models\Order::join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->select('orders.doctor_id')
        ->where('orders.status', 'delivered')
        ->whereNotNull('orders.doctor_id')
        ->whereMonth('orders.created_at', now()->month)
        ->whereYear('orders.created_at', now()->year)
        ->groupBy('orders.doctor_id')
        ->get()->count();

    $rankInfo = [
        'monthly_rank'  => $rMonthlyRank,
        'monthly_sales' => $rMonthlyCount,
        'total_doctors' => $rTotalDoctors,
        'monthly_tier'  => match(true) {
            $rMonthlyCount >= 300 => ['name' => 'Elite',    'badge' => '💎', 'bg_class' => 'bg-purple-100 text-purple-800'],
            $rMonthlyCount >= 150 => ['name' => 'Platinum', 'badge' => '🥇', 'bg_class' => 'bg-gray-100 text-gray-800'],
            $rMonthlyCount >= 75  => ['name' => 'Gold',     'badge' => '🥈', 'bg_class' => 'bg-yellow-100 text-yellow-800'],
            $rMonthlyCount >= 30  => ['name' => 'Silver',   'badge' => '🥉', 'bg_class' => 'bg-gray-100 text-gray-600'],
            default               => ['name' => 'Bronze',   'badge' => '🏅', 'bg_class' => 'bg-orange-100 text-orange-800'],
        },
    ];
}
@endphp

<div class="space-y-8">
    <!-- Referral Code Card -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-primary-100 text-sm font-medium">Your Referral Code</p>
                <div class="flex items-center gap-3 mt-2">
                    <code class="text-2xl font-bold bg-white/20 px-4 py-2 rounded-lg">{{ $user->unique_code ?? 'N/A' }}</code>
                    <button onclick="copyReferralCode()" class="p-2 bg-white/20 hover:bg-white/30 rounded-lg transition-colors" title="Copy to clipboard">
                        <i data-lucide="copy" class="w-5 h-5"></i>
                    </button>
                </div>
                <p class="text-primary-100 text-sm mt-2">Share this code with patients to earn rewards</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('doctor.spin.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-primary-700 rounded-lg font-medium hover:bg-primary-50 transition-colors">
                    <i data-lucide="gift" class="w-5 h-5 mr-2"></i>
                    Spin & Win
                    @if($canSpin)
                        <span class="ml-2 w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Direct Sales -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Direct Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($directSales ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">This month</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Referral Sales -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Referral Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($referralSales ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">This month</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($totalSales ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Combined</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Target Progress -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-500">Monthly Target</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($progress ?? 0, 0) }}%</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-primary-500 h-2 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $currentProducts }} / {{ $targetProducts }} Products</p>
                    @if($remainingSpins > 0)
                        <p class="text-xs text-green-600 mt-1 font-medium">{{ $remainingSpins }} spin(s) available!</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center ml-4">
                    <i data-lucide="target" class="w-6 h-6 text-amber-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left: Leaderboard Rank Card + Recent Orders -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Leaderboard Rank Card -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                            <i data-lucide="trophy" class="w-6 h-6 text-yellow-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Monthly Leaderboard Rank</h3>
                            <p class="text-xs text-gray-400">{{ now()->format('F Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('leaderboard.monthly') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                        View Full Board
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>

                @if(($rankInfo['monthly_sales'] ?? 0) > 0)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <!-- Rank Badge -->
                        <div class="flex-shrink-0 w-20 h-20 rounded-2xl bg-gradient-to-br from-yellow-400 to-yellow-600 flex flex-col items-center justify-center shadow">
                            <span class="text-white text-xs font-semibold uppercase tracking-wider">Rank</span>
                            <span class="text-white text-2xl font-bold leading-none">#{{ $rankInfo['monthly_rank'] }}</span>
                        </div>
                        <!-- Details -->
                        <div class="flex-1 space-y-2">
                            <p class="text-sm text-gray-500">
                                You are ranked <span class="font-bold text-gray-900">#{{ $rankInfo['monthly_rank'] }}</span>
                                out of <span class="font-bold text-gray-900">{{ $rankInfo['total_doctors'] }}</span> doctor(s) this month
                            </p>
                            <p class="text-sm text-gray-500">
                                Products Sold This Month:
                                <span class="font-bold text-gray-900">{{ $rankInfo['monthly_sales'] }}</span> units
                            </p>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $rankInfo['monthly_tier']['bg_class'] }}">
                                {{ $rankInfo['monthly_tier']['badge'] }} {{ $rankInfo['monthly_tier']['name'] }}
                            </span>
                        </div>
                        <!-- Tier Progress Hint -->
                        @php
                            $nextThresholds = [30 => 'Silver', 75 => 'Gold', 150 => 'Platinum', 300 => 'Elite'];
                            $nextLabel = null;
                            $nextNeeded = 0;
                            foreach ($nextThresholds as $threshold => $label) {
                                if ($rankInfo['monthly_sales'] < $threshold) {
                                    $nextLabel = $label;
                                    $nextNeeded = $threshold - $rankInfo['monthly_sales'];
                                    break;
                                }
                            }
                        @endphp
                        @if($nextLabel)
                            <div class="flex-shrink-0 text-center bg-gray-50 rounded-xl px-4 py-3">
                                <p class="text-xs text-gray-400">Next Tier</p>
                                <p class="text-sm font-bold text-gray-900">{{ $nextLabel }}</p>
                                <p class="text-xs text-gray-500 mt-1"><span class="font-semibold text-primary-600">{{ $nextNeeded }}</span> more products</p>
                            </div>
                        @else
                            <div class="flex-shrink-0 text-center bg-purple-50 rounded-xl px-4 py-3">
                                <p class="text-xs text-purple-500">Top Tier</p>
                                <p class="text-sm font-bold text-purple-800">💎 Elite</p>
                                <p class="text-xs text-purple-400 mt-1">Maximum level!</p>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Zero products state: card still visible, encouraging message shown -->
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gray-200 flex flex-col items-center justify-center">
                            <i data-lucide="trophy" class="w-7 h-7 text-gray-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-700">You have not sold any products this month yet.</p>
                            <p class="text-xs text-gray-400 mt-1">Start ordering to appear on the leaderboard.</p>
                            <a href="{{ route('products.catalog') }}" class="inline-block mt-2 px-3 py-1.5 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition-colors">
                                Browse Products
                            </a>
                        </div>
                        <div class="flex-shrink-0 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-800">
                                🏅 Bronze
                            </span>
                            <p class="text-xs text-gray-400 mt-1">Current Tier</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-500 mt-1">Your recent purchases</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                        View All
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'approved') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <i data-lucide="shopping-bag" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                        <p class="text-sm font-medium text-gray-900">No orders yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div><!-- end xl:col-span-2 wrapper -->

        <!-- Side Panel -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('products.catalog') }}" class="flex items-center p-3 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors">
                        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="package" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Browse Products</p>
                            <p class="text-xs text-gray-500">View our catalog</p>
                        </div>
                    </a>
                    <a href="{{ route('doctor.referrals') }}" class="flex items-center p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="users" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">View Referrals</p>
                            <p class="text-xs text-gray-500">Check referral sales</p>
                        </div>
                    </a>
                    <a href="{{ route('doctor.reports.referral-sales') }}" class="flex items-center p-3 rounded-xl bg-teal-50 hover:bg-teal-100 transition-colors">
                        <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">My Referral Sales</p>
                            <p class="text-xs text-gray-500">Product &amp; quantity report</p>
                        </div>
                    </a>
                    <a href="{{ route('doctor.targets.index') }}" class="flex items-center p-3 rounded-xl bg-amber-50 hover:bg-amber-100 transition-colors">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="target" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">My Targets</p>
                            <p class="text-xs text-gray-500">Track your progress</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Referral Orders -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Referral Orders</h3>
                    <a href="{{ route('doctor.referrals') }}" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($referralOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->name ?? 'N/A' }}</p>
                            </div>
                            <span class="text-sm font-medium text-gray-900">₹{{ number_format($order->total_amount, 0) }}</span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No referrals yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Available Stores -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Available Stores</h3>
                    <span class="text-xs text-gray-400">{{ $stores->count() }} stores</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Store Name</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Owner</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stores as $store)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 font-medium text-gray-900">{{ $store->store_name }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $store->owner_name }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $store->phone }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $store->area ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-6 text-center text-gray-400">
                                        <i data-lucide="store" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                        <p class="text-sm">No stores available</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralCode() {
    const code = '{{ $user->unique_code }}';
    navigator.clipboard.writeText(code).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = 'Referral code copied!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}
</script>
@endsection
