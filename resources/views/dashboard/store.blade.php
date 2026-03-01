@extends('layouts.app')

@section('title', 'Store Dashboard')
@section('page-title', 'Store Dashboard')
@section('page-description', 'Welcome back, ' . auth()->user()->name)

@section('content')
@php
$user = auth()->user();
$store = $user->store;

// Calculate stats using Orders table (which has total_amount column)
// Today's sales - orders placed today linked to this store
$todaySales = \App\Models\Order::where('store_id', $user->id)
    ->where('status', 'delivered')
    ->whereDate('created_at', today())
    ->sum('total_amount');

// Referral sales - orders with doctor_id linked to this store this month
$referralSales = \App\Models\Order::where('store_id', $user->id)
    ->whereNotNull('doctor_id')
    ->where('status', 'delivered')
    ->whereMonth('created_at', now()->month)
    ->sum('total_amount');

$pendingOrders = \App\Models\Order::where('store_id', $user->id)
    ->whereIn('status', ['pending', 'approved'])
    ->count();

$lowStockCount = $store ? $store->stocks()->where('quantity', '<=', 10)->count() : 0;
@endphp

<div class="space-y-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Today's Sales -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Today's Sales</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">₹{{ number_format($todaySales ?? 0, 0) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Direct sales</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="indian-rupee" class="w-6 h-6 text-green-600"></i>
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
                    <i data-lucide="user-check" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ $pendingOrders ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Awaiting action</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-2xl shadow-card p-6 border border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-900' }} mt-2">{{ $lowStockCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">Need attention</p>
                </div>
                <div class="w-12 h-12 {{ $lowStockCount > 0 ? 'bg-red-50' : 'bg-gray-50' }} rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-500' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold">Quick Actions</h3>
                <p class="text-primary-100 text-sm">Manage your store efficiently</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-primary-700 rounded-lg font-medium hover:bg-primary-50 transition-colors">
                    <i data-lucide="shopping-cart" class="w-5 h-5 mr-2"></i>
                    Place Order
                </a>
                <a href="{{ route('store.reports.sales') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg font-medium hover:bg-white/30 transition-colors">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 mr-2"></i>
                    Sales Report
                </a>
                <a href="{{ route('store.stock.index') }}" class="inline-flex items-center px-4 py-2 bg-white/20 text-white rounded-lg font-medium hover:bg-white/30 transition-colors">
                    <i data-lucide="package" class="w-5 h-5 mr-2"></i>
                    Manage Stock
                </a>
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
                        <p class="text-sm text-gray-500 mt-1">Orders placed to your store</p>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $recentOrders = \App\Models\Order::where('store_id', $user->id)
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $order->user->name ?? 'N/A' }}</span>
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
            <!-- Stock Summary -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Stock Summary</h3>
                    <a href="{{ route('store.stock.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Manage</a>
                </div>
                @php
                    $stockSummary = $store ? $store->stocks()->with('product')->take(5)->get() : collect();
                @endphp
                <div class="space-y-3">
                    @forelse($stockSummary as $stock)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $stock->product->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Min: {{ $stock->min_stock }}</p>
                            </div>
                            <span class="text-sm font-bold {{ $stock->quantity <= $stock->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                {{ $stock->quantity }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <i data-lucide="package" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No stock data</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Referral Doctors -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Referral Doctors</h3>
                    <a href="{{ route('store.referrals') }}" class="text-sm text-primary-600 hover:text-primary-700">View All</a>
                </div>
                @php
                    $referralDoctors = \App\Models\User::role('Doctor')
                        ->whereHas('orders', function($q) use ($user) {
                            $q->where('store_id', $user->id);
                        })
                        ->take(5)
                        ->get();
                @endphp
                <div class="space-y-3">
                    @forelse($referralDoctors as $doctor)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i data-lucide="user-md" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $doctor->name }}</p>
                                <p class="text-xs text-gray-500">{{ $doctor->unique_code }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <i data-lucide="users" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No referral doctors</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
