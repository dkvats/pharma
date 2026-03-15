@extends('layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Order Details</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Orders
        </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Info -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h2>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Commission</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">₹{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right">₹{{ number_format($item->commission, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        @php
                            $subtotal = $order->total_amount + ($order->discount_amount ?? 0);
                        @endphp
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-gray-600">Subtotal:</td>
                            <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-green-600">
                                Discount @if($order->offer)<span class="text-xs">({{ $order->offer->title }})</span>@endif:
                            </td>
                            <td class="px-4 py-2 text-right text-green-600 font-medium">-₹{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Total Payable:</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($order->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Order Notes</h2>
                    <p class="text-gray-600">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Status</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $order->status == 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $order->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $order->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sale Type:</span>
                        <span class="text-gray-900">{{ $order->sale_type_label }}</span>
                    </div>
                    @if($order->referral_code)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Referral Code:</span>
                            <span class="text-gray-900">{{ $order->referral_code }}</span>
                        </div>
                    @endif
                    @if($order->hasPrescription())
                        <div class="mt-4 pt-4 border-t">
                            <span class="text-gray-600 block mb-2">Prescription:</span>
                            <div class="flex gap-2">
                                <a href="{{ route('orders.prescription.view', $order) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                    @if(Str::endsWith($order->prescription, '.pdf'))
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>View PDF</span>
                                    @else
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>View Image</span>
                                    @endif
                                </a>
                                <a href="{{ route('orders.prescription.download', $order) }}" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>Download</span>
                                </a>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $order->prescription_uploaded_at->format('M d, Y') }}</p>
                        </div>
                    @endif
                </div>

                <!-- Actions -->
                @if($order->isPending())
                    <div class="mt-4 space-y-2">
                        <form action="{{ route('admin.orders.approve', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Approve Order
                            </button>
                        </form>
                        <form action="{{ route('admin.orders.reject', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg">
                                Reject Order
                            </button>
                        </form>
                    </div>
                @elseif($order->isApproved())
                    <div class="mt-4">
                        @if($order->bill_generated)
                            <form action="{{ route('admin.orders.deliver', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                                    Mark as Delivered
                                </button>
                            </form>
                        @else
                            <button type="button" disabled
                                title="Generate bill before delivery"
                                class="w-full bg-gray-300 text-gray-500 cursor-not-allowed font-semibold py-2 px-4 rounded-lg">
                                Mark as Delivered
                            </button>
                            <p class="text-xs text-amber-600 mt-2 text-center">
                                Bill must be generated before delivery.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
                <div class="space-y-2">
                    <p><span class="text-gray-600">Name:</span> <span class="text-gray-900">{{ $order->user->name ?? 'N/A' }}</span></p>
                    <p><span class="text-gray-600">Email:</span> <span class="text-gray-900">{{ $order->user->email ?? 'N/A' }}</span></p>
                    <p><span class="text-gray-600">Phone:</span> <span class="text-gray-900">{{ $order->user->phone ?? 'N/A' }}</span></p>
                </div>
            </div>

            <!-- Doctor/Store Info -->
            @if($order->doctor)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Doctor Information</h2>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">Name:</span> <span class="text-gray-900">{{ $order->doctor->name }}</span></p>
                        <p><span class="text-gray-600">Code:</span> <span class="text-gray-900">{{ $order->doctor->code ?? 'N/A' }}</span></p>
                    </div>
                </div>
            @endif

            @if($order->store)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Store Information</h2>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">Name:</span> <span class="text-gray-900">{{ $order->store->name }}</span></p>
                        <p><span class="text-gray-600">Code:</span> <span class="text-gray-900">{{ $order->store->code ?? 'N/A' }}</span></p>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Timeline</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ordered:</span>
                        <span class="text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    @if($order->approved_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Approved:</span>
                            <span class="text-gray-900">{{ $order->approved_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Approved By:</span>
                            <span class="text-gray-900">{{ $order->approvedBy->name ?? 'N/A' }}</span>
                        </div>
                    @endif
                    @if($order->delivered_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivered:</span>
                            <span class="text-gray-900">{{ $order->delivered_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
