@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Order {{ $order->order_number }}</h1>
                <p class="text-gray-500">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Orders
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Order Status</h2>
                    <div class="mt-2">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $order->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Sale Type</p>
                    <p class="font-semibold text-gray-800">{{ $order->sale_type_label }}</p>
                </div>
            </div>
            
            <!-- Cancel Order Button - End User Only, Pending Only -->
            @if(auth()->user()->hasRole('End User') && $order->status === 'pending' && $order->user_id === auth()->id())
                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end">
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">
                            Cancel Order
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow overflow-x-auto mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Order Items</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->product->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                ₹{{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                ₹{{ number_format($item->subtotal, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    @php
                        $subtotal = $order->total_amount + ($order->discount_amount ?? 0);
                    @endphp
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-gray-600">Subtotal:</td>
                        <td class="px-6 py-3 text-right text-gray-900">₹{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-green-600">
                            Discount Applied @if($order->offer)<span class="text-xs">({{ $order->offer->title }})</span>@endif:
                        </td>
                        <td class="px-6 py-3 text-right text-green-600 font-medium">-₹{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-800">Total Payable:</td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Referral Info -->
        @if($order->referral_code)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Referral Information</h2>
                <p class="text-gray-600">Referred by code: <span class="font-semibold">{{ $order->referral_code }}</span></p>
                @if($order->doctor)
                    <p class="text-gray-600 mt-2">Doctor: {{ $order->doctor->name }}</p>
                @endif
                @if($order->store)
                    <p class="text-gray-600 mt-2">Store: {{ $order->store->name }}</p>
                @endif
            </div>
        @endif

        <!-- Prescription -->
        @if($order->hasPrescription())
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Prescription</h2>
                <div class="flex items-center gap-4">
                    @if(Str::endsWith($order->prescription, '.pdf'))
                        <div class="flex items-center gap-2">
                            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-600">PDF Document</span>
                        </div>
                    @else
                        <img src="{{ route('orders.prescription.view', $order) }}" alt="Prescription" class="h-32 w-auto rounded-lg border">
                    @endif
                    <div class="flex gap-2">
                        <a href="{{ route('orders.prescription.view', $order) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                            View
                        </a>
                        <a href="{{ route('orders.prescription.download', $order) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                            Download
                        </a>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Uploaded on {{ $order->prescription_uploaded_at->format('M d, Y h:i A') }}</p>
            </div>
        @endif

        <!-- Notes -->
        @if($order->notes)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Additional Notes</h2>
                <p class="text-gray-600">{{ $order->notes }}</p>
            </div>
        @endif

        <!-- Timeline -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Timeline</h2>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600">1</span>
                    </div>
                    <div class="ml-4">
                        <p class="font-medium text-gray-800">Order Placed</p>
                        <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                @if($order->approved_at)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600">2</span>
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Order Approved</p>
                            <p class="text-sm text-gray-500">{{ $order->approved_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->delivered_at)
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-green-600">3</span>
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-800">Order Delivered</p>
                            <p class="text-sm text-gray-500">{{ $order->delivered_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
