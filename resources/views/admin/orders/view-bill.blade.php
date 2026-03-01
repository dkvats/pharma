@extends('layouts.app')

@section('title', 'View Bill - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bill for Order #{{ $order->order_number }}</h1>
        <div class="space-x-4">
            @if($order->user?->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->user->phone) }}?text={{ urlencode('Hello, your bill for order ' . $order->order_number . ' is ready. Total: ₹' . number_format($order->total_amount, 2) . '. View bill: ' . route('admin.orders.view-bill', $order)) }}" 
               target="_blank"
               class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg">
                📱 Send on WhatsApp
            </a>
            @endif
            <a href="{{ route('admin.orders.download-bill', $order) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                Download PDF
            </a>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
                Back to Orders
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-8">
        <div class="text-center border-b-2 border-gray-800 pb-6 mb-6">
            <h2 class="text-3xl font-bold mb-2">Pharma Management System</h2>
            <p class="text-gray-600">Invoice/Bill</p>
            <p class="text-xl font-semibold mt-4">Bill #{{ $order->order_number }}</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-gray-600">Order Number:</p>
                <p class="font-semibold">{{ $order->order_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-600">Date:</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600">Status:</p>
                <p class="font-semibold">{{ $order->status_label }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-600">Sale Type:</p>
                <p class="font-semibold">{{ $order->sale_type_label }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="font-bold text-lg mb-2">Customer Information</h3>
            <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
        </div>

        @if($order->doctor)
        <div class="mb-6">
            <h3 class="font-bold text-lg mb-2">Doctor</h3>
            <p>{{ $order->doctor->name }} ({{ $order->doctor->code ?? 'N/A' }})</p>
        </div>
        @endif

        @if($order->store)
        <div class="mb-6">
            <h3 class="font-bold text-lg mb-2">Store</h3>
            <p>{{ $order->store->name }} ({{ $order->store->code ?? 'N/A' }})</p>
        </div>
        @endif

        <table class="w-full border-collapse border border-gray-300 mt-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left">Product</th>
                    <th class="border border-gray-300 px-4 py-2 text-right">Price</th>
                    <th class="border border-gray-300 px-4 py-2 text-right">Qty</th>
                    <th class="border border-gray-300 px-4 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="3" class="border border-gray-300 px-4 py-2 text-right">Total Amount:</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if($order->notes)
        <div class="mt-6">
            <h3 class="font-bold text-lg mb-2">Notes</h3>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
        @endif

        <div class="mt-8 text-center text-gray-600 text-sm">
            <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
            <p class="mt-2">Thank you for your business!</p>
        </div>
    </div>
</div>
@endsection
