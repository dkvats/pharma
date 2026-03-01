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
