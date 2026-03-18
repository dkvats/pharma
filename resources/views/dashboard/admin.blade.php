@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-description', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="space-y-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_users'] ?? 0 }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium flex items-center">
                            <i class="fas fa-arrow-up w-4 h-4 mr-1"></i>
                            +12%
                        </span>
                        <span class="text-gray-500 ml-2">vs last month</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Doctors -->
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Doctors</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_doctors'] ?? 0 }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 font-medium">{{ $stats['pending_doctors'] ?? 0 }} pending</span>
                        <span class="text-gray-500 ml-2">approval</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-md text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Stores -->
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Stores</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_stores'] ?? 0 }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium flex items-center">
                            <i class="fas fa-check-circle w-4 h-4 mr-1"></i>
                            Active
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-store text-xl text-amber-600"></i>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="bg-white rounded-2xl shadow p-6 border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Revenue</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">₹{{ number_format($stats['today_revenue'] ?? 0, 0) }}</p>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium flex items-center">
                            <i class="fas fa-arrow-up w-4 h-4 mr-1"></i>
                            +8.5%
                        </span>
                        <span class="text-gray-500 ml-2">vs yesterday</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Orders Today -->
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-primary-100 text-sm font-medium">Orders Today</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['today_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex justify-between text-sm">
                    <span class="text-primary-100">Pending</span>
                    <span class="font-semibold">{{ $stats['pending_orders'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Products</p>
                    <p class="text-3xl font-bold mt-1 text-white">{{ $stats['total_products'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <i data-lucide="package" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex justify-between text-sm">
                    <span class="text-green-100">Low Stock</span>
                    <span class="font-semibold">{{ $stats['low_stock_products'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- MRs -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Medical Reps</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['total_mrs'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <i data-lucide="briefcase" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <div class="flex justify-between text-sm">
                    <span class="text-purple-100">Active Today</span>
                    <span class="font-semibold">{{ $stats['active_mrs'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Alert Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Expiring Soon -->
        <a href="{{ route('admin.inventory-reports.expiring') }}" class="block bg-yellow-50 border border-yellow-200 rounded-2xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Expiring in 30 Days</p>
                    <p class="text-3xl font-bold text-yellow-800 mt-1">{{ $stats['expiring_soon'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600 mt-1">batches</p>
                </div>
                <div class="w-12 h-12 bg-yellow-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-yellow-700"></i>
                </div>
            </div>
        </a>

        <!-- Expired Batches -->
        <a href="{{ route('admin.inventory-reports.expired') }}" class="block bg-red-50 border border-red-200 rounded-2xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700">Expired Batches</p>
                    <p class="text-3xl font-bold text-red-800 mt-1">{{ $stats['expired_batches'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 mt-1">need removal</p>
                </div>
                <div class="w-12 h-12 bg-red-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-700"></i>
                </div>
            </div>
        </a>

        <!-- Out of Stock -->
        <a href="{{ route('admin.inventory-reports.low-stock') }}" class="block bg-orange-50 border border-orange-200 rounded-2xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-700">Low / Out of Stock</p>
                    <p class="text-3xl font-bold text-orange-800 mt-1">{{ $stats['low_stock_products'] ?? 0 }}</p>
                    <p class="text-xs text-orange-600 mt-1">products</p>
                </div>
                <div class="w-12 h-12 bg-orange-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-6 h-6 text-orange-700"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Inventory Distribution Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <!-- Warehouse Stock -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Warehouse Stock</p>
                    <p class="text-3xl font-bold text-blue-800 mt-1">{{ number_format($stats['warehouse_stock'] ?? 0) }}</p>
                    <p class="text-xs text-blue-600 mt-1">active batch units</p>
                </div>
                <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="warehouse" class="w-6 h-6 text-blue-700"></i>
                </div>
            </div>
        </div>

        <!-- Store Stock -->
        <div class="bg-teal-50 border border-teal-200 rounded-2xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-teal-700">Store Stock</p>
                    <p class="text-3xl font-bold text-teal-800 mt-1">{{ number_format($stats['store_stock'] ?? 0) }}</p>
                    <p class="text-xs text-teal-600 mt-1">allocated units</p>
                </div>
                <div class="w-12 h-12 bg-teal-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="store" class="w-6 h-6 text-teal-700"></i>
                </div>
            </div>
        </div>

        <!-- Pending Returns -->
        <a href="{{ route('admin.expired-batches.index') }}" class="block bg-yellow-50 border border-yellow-200 rounded-2xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Pending Returns</p>
                    <p class="text-3xl font-bold text-yellow-800 mt-1">{{ $stats['pending_returns'] ?? 0 }}</p>
                    <p class="text-xs text-yellow-600 mt-1">expired batches</p>
                </div>
                <div class="w-12 h-12 bg-yellow-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="undo" class="w-6 h-6 text-yellow-700"></i>
                </div>
            </div>
        </a>

        <!-- Out of Stock -->
        <a href="{{ route('admin.inventory-reports.low-stock') }}" class="block bg-red-50 border border-red-200 rounded-2xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700">Out of Stock</p>
                    <p class="text-3xl font-bold text-red-800 mt-1">{{ $stats['out_of_stock'] ?? 0 }}</p>
                    <p class="text-xs text-red-600 mt-1">products</p>
                </div>
                <div class="w-12 h-12 bg-red-200 rounded-xl flex items-center justify-center">
                    <i data-lucide="package-x" class="w-6 h-6 text-red-700"></i>
                </div>
            </div>
        </a>
    </div>

    @if($expiring_batches->isNotEmpty())
    <!-- Expiring Soon Quick View -->
    <div class="mt-6 bg-white rounded-2xl shadow-card border border-yellow-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Batches Expiring Soon</h3>
            <a href="{{ route('admin.inventory-reports.expiring') }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium">View All &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-2 pr-4 font-medium text-gray-500">Product</th>
                        <th class="text-left py-2 pr-4 font-medium text-gray-500">Batch</th>
                        <th class="text-left py-2 pr-4 font-medium text-gray-500">Qty</th>
                        <th class="text-left py-2 font-medium text-gray-500">Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($expiring_batches as $batch)
                    <tr>
                        <td class="py-2 pr-4 font-medium text-gray-800">{{ $batch->product->name }}</td>
                        <td class="py-2 pr-4 text-gray-600">{{ $batch->batch_number }}</td>
                        <td class="py-2 pr-4 text-gray-600">{{ $batch->quantity }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $batch->getExpiryBadgeClass() }}">
                                {{ $batch->expiry_date->format('d M Y') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-500 mt-1">Latest orders across all channels</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
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
                        @forelse($recent_orders ?? [] as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $order->order_number }}</span>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $order->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'approved') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'rejected') bg-red-100 text-red-800
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
                                        <p class="text-xs text-gray-500 mt-1">Orders will appear here</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions & Activity -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.products.create') }}" class="flex items-center p-3 rounded-xl bg-primary-50 hover:bg-primary-100 transition-colors">
                        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="plus" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Add Product</p>
                            <p class="text-xs text-gray-500">Create new product listing</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.doctors.approval.index') }}" class="flex items-center p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="user-check" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Approve Doctors</p>
                            <p class="text-xs text-gray-500">{{ $stats['pending_doctors'] ?? 0 }} pending approvals</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.reports.dashboard') }}" class="flex items-center p-3 rounded-xl bg-green-50 hover:bg-green-100 transition-colors">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                            <i data-lucide="bar-chart-2" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">View Reports</p>
                            <p class="text-xs text-gray-500">Analytics & insights</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-card border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    @forelse($recent_activity ?? [] as $activity)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="activity" class="w-4 h-4 text-gray-500"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400">
                            <i data-lucide="clock" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
