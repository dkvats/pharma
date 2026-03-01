@extends('layouts.app')

@section('title', 'My Performance Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Performance Report</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Direct Orders</p>
            <p class="text-2xl font-bold text-blue-600">{{ $directOrdersCount ?? 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Direct Sales Value</p>
            <p class="text-2xl font-bold text-green-600">₹{{ number_format($directOrdersValue ?? 0, 2) }}</p>
        </div>
        <div class="bg-purple-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Referral Orders</p>
            <p class="text-2xl font-bold text-purple-600">{{ $referralOrdersCount ?? 0 }}</p>
            <p class="text-xs text-gray-500">₹{{ number_format($referralOrdersValue ?? 0, 0) }}</p>
        </div>
        <div class="bg-orange-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Contribution</p>
            <p class="text-2xl font-bold text-orange-600">{{ $totalContribution ?? 0 }}</p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Recent Orders</h2>
        </div>

        @if($recentOrders->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p>No orders found.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->order_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($order->user_id == auth()->id())
                                    <span class="text-green-600 font-semibold">Direct</span>
                                @elseif($order->doctor_id == auth()->id())
                                    <span class="text-blue-600 font-semibold">Referral</span>
                                @else
                                    <span class="text-gray-500">Unknown</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($order->items->count() > 0)
                                    {{ $order->items->map(fn($item) => $item->product->name ?? 'N/A')->join(', ') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
