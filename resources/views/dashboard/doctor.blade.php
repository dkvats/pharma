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
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-gray-100">
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
