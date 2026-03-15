@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Orders</h1>
        <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
            + Place New Order
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ₹{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $order->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $order->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $order->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                            @if(auth()->user()->hasRole('End User') && $order->status === 'pending' && $order->user_id === auth()->id())
                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Cancel this order?');">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No orders found. <a href="{{ route('products.catalog') }}" class="text-blue-600 hover:underline">Browse products</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
