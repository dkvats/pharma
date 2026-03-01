@extends('layouts.app')

@section('title', 'MR Dashboard')
@section('page-title', 'MR Dashboard')
@section('page-description', 'Welcome back, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">
    <!-- Quick Action Buttons -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('mr.doctors.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition">
            <i class="fas fa-user-plus text-2xl mb-2"></i>
            <p class="font-semibold">Register Doctor</p>
        </a>
        <a href="{{ route('mr.visits.create') }}" class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition">
            <i class="fas fa-clipboard-check text-2xl mb-2"></i>
            <p class="font-semibold">Add Visit</p>
        </a>
        <a href="{{ route('mr.orders.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition">
            <i class="fas fa-cart-plus text-2xl mb-2"></i>
            <p class="font-semibold">Book Order</p>
        </a>
        <a href="{{ route('mr.samples.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition">
            <i class="fas fa-vial text-2xl mb-2"></i>
            <p class="font-semibold">Add Sample</p>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-md text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Doctors Covered Today</p>
                    <p class="text-2xl font-bold">{{ $stats['doctors_covered_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">New Doctors Today</p>
                    <p class="text-2xl font-bold">{{ $stats['new_doctors_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Orders Today</p>
                    <p class="text-2xl font-bold">{{ $stats['orders_today'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Doctors</p>
                    <p class="text-2xl font-bold">{{ $stats['total_doctors'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Upcoming Visits</p>
                    <p class="text-2xl font-bold">{{ $stats['upcoming_visits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Pending Orders</p>
                    <p class="text-2xl font-bold">{{ $stats['pending_orders'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Today's Schedule</h3>
            </div>
            <div class="p-6">
                @if($todayVisits->count() > 0)
                    <div class="space-y-3">
                        @foreach($todayVisits as $visit)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">{{ $visit->doctor->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $visit->doctor->clinic_name }}</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm 
                                    {{ $visit->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($visit->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No visits scheduled for today.</p>
                @endif
            </div>
        </div>

        <!-- Recent Visits -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Recent Visits</h3>
                <a href="{{ route('mr.visits.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
            </div>
            <div class="p-6">
                @if($recentVisits->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentVisits as $visit)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <p class="font-medium">{{ $visit->doctor->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $visit->visit_date->format('M d, Y') }}</p>
                                </div>
                                <a href="{{ route('mr.visits.show', $visit) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent visits.</p>
                @endif
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow lg:col-span-2">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold">Recent Orders</h3>
                <a href="{{ route('mr.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
            </div>
            <div class="p-6">
                @if($recentOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2">Order #</th>
                                    <th class="text-left py-2">Doctor</th>
                                    <th class="text-left py-2">Date</th>
                                    <th class="text-right py-2">Amount</th>
                                    <th class="text-center py-2">Status</th>
                                    <th class="text-center py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr class="border-b">
                                        <td class="py-3">{{ $order->order_number }}</td>
                                        <td class="py-3">{{ $order->doctor->name }}</td>
                                        <td class="py-3">{{ $order->ordered_at?->format('M d, Y') }}</td>
                                        <td class="py-3 text-right">₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-1 rounded text-sm 
                                                {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ !in_array($order->status, ['delivered', 'pending', 'cancelled']) ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center">
                                            <a href="{{ route('mr.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent orders.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
