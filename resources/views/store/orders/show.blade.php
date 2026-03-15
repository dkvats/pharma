@extends('layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')
@section('page-description', 'View order information')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Order #{{ $order->order_number ?? $order->id }}
        </h1>
        <a href="{{ route('store.orders.index') }}" class="text-blue-600 hover:underline">
            &larr; Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="px-2 py-1 rounded text-white text-xs
                    @if($order->status == 'pending') bg-yellow-500
                    @elseif($order->status == 'approved') bg-blue-500
                    @elseif($order->status == 'delivered') bg-green-500
                    @elseif($order->status == 'rejected') bg-red-500
                    @elseif($order->status == 'cancelled') bg-gray-600
                    @else bg-gray-500
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
                @if($order->status === 'cancelled')
                    <p class="text-xs text-gray-500 mt-1">
                        Cancelled on {{ $order->cancelled_at?->format('d M Y, h:i A') ?? 'N/A' }}
                    </p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600">Date</p>
                <p class="font-medium">{{ $order->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="font-bold text-lg">₹{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        <hr class="my-4">

        <h2 class="font-semibold mb-3">Products</h2>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Product</th>
                    <th class="p-2 text-left">Qty</th>
                    <th class="p-2 text-left">Price</th>
                    <th class="p-2 text-left">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach($order->items as $item)
                <tr class="border-b">
                    <td class="p-2">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="p-2">{{ $item->quantity }}</td>
                    <td class="p-2">₹{{ number_format($item->price, 2) }}</td>
                    <td class="p-2 font-semibold">
                        ₹{{ number_format($item->subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-right mt-4 pt-4 border-t">
            @php
                $subtotal = $order->total_amount + ($order->discount_amount ?? 0);
            @endphp
            <p class="text-gray-600">Subtotal: ₹{{ number_format($subtotal, 2) }}</p>
            @if($order->discount_amount > 0)
                <p class="text-green-600">
                    Discount @if($order->offer)<span class="text-xs">({{ $order->offer->title }})</span>@endif: -₹{{ number_format($order->discount_amount, 2) }}
                </p>
            @endif
            <p class="text-lg font-bold mt-2">
                Total Payable: ₹{{ number_format($order->total_amount, 2) }}
            </p>
        </div>

        @if($order->notes)
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <p class="text-sm text-gray-600">Notes:</p>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        {{-- Store Cancellation Actions --}}
        @if($order->status === 'pending')
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded">
                <h3 class="font-semibold text-red-800 mb-2">Cancel Order</h3>
                <p class="text-sm text-red-600 mb-3">
                    You can cancel this order while it is pending. Stock will be restored.
                </p>
                <form method="POST" action="{{ route('store.orders.cancel', $order) }}" 
                      onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Cancel Order
                    </button>
                </form>
            </div>
        @endif

        @if($order->status === 'approved')
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded">
                @if($pendingRequest)
                    <h3 class="font-semibold text-yellow-800 mb-2">Cancellation Request Pending</h3>
                    <p class="text-sm text-yellow-700">
                        You have submitted a cancellation request for this order. 
                        Please wait for Admin approval.
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Submitted on {{ $pendingRequest->created_at->format('d M Y, h:i A') }}
                    </p>
                @else
                    <h3 class="font-semibold text-yellow-800 mb-2">Request Cancellation</h3>
                    <p class="text-sm text-yellow-600 mb-3">
                        This order is approved. To cancel, you must submit a request to Admin for review.
                    </p>
                    <a href="{{ route('store.orders.request-cancel', $order) }}" 
                       class="inline-block px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                        Request Cancellation
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
