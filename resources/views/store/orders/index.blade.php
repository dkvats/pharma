@extends('layouts.app')

@section('title', 'My Orders')
@section('page-title', 'My Orders')
@section('page-description', 'View all your orders')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">My Orders</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Order #</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3 text-left">Amount</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($orders as $order)
                <tr class="border-b">
                    <td class="p-3 font-medium">
                        {{ $order->order_number ?? $order->id }}
                    </td>

                    <td class="p-3">
                        {{ $order->created_at->format('d M Y') }}
                    </td>

                    <td class="p-3 font-semibold">
                        ₹{{ number_format($order->total_amount, 2) }}
                    </td>

                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-white text-xs
                            @if($order->status == 'pending') bg-yellow-500
                            @elseif($order->status == 'approved') bg-blue-500
                            @elseif($order->status == 'delivered') bg-green-500
                            @elseif($order->status == 'rejected') bg-red-500
                            @else bg-gray-500
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                    <td class="p-3">
                        <a href="{{ route('store.orders.show', $order) }}"
                           class="text-blue-600 hover:underline">
                           View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">
                        No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
