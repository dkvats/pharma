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
                    @else bg-gray-500
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
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
            <p class="text-lg font-bold">
                Total: ₹{{ number_format($order->total_amount, 2) }}
            </p>
        </div>

        @if($order->notes)
        <div class="mt-6 p-4 bg-gray-50 rounded">
            <p class="text-sm text-gray-600">Notes:</p>
            <p>{{ $order->notes }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
