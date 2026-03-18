@extends('layouts.app')

@section('title', 'Purchase Order: ' . $purchase->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Purchase Order</h1>
                <p class="text-gray-600 font-mono">{{ $purchase->order_number }}</p>
            </div>
            <div class="flex gap-2">
                @if(!in_array($purchase->status, ['fully_received', 'cancelled']))
                    <a href="{{ route('admin.purchases.receive.form', $purchase) }}" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg">Receive Goods</a>
                @endif
                <a href="{{ route('admin.purchases.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">Back</a>
            </div>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs text-gray-500 uppercase">Supplier</label>
                    <p class="font-semibold">{{ $purchase->supplier->name }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Order Date</label>
                    <p class="font-semibold">{{ $purchase->order_date->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Status</label>
                    <p>
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'ordered' => 'bg-blue-100 text-blue-800',
                                'partially_received' => 'bg-yellow-100 text-yellow-800',
                                'fully_received' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$purchase->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst(str_replace('_', ' ', $purchase->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-xs text-gray-500 uppercase">Created By</label>
                    <p class="font-semibold">{{ $purchase->creator->name ?? 'N/A' }}</p>
                </div>
            </div>
            @if($purchase->notes)
                <div class="mt-4 pt-4 border-t">
                    <label class="text-xs text-gray-500 uppercase">Notes</label>
                    <p class="text-gray-700">{{ $purchase->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Items -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ordered</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Received</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Remaining</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">MRP</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Batch/Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchase->items as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->product->name }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-center {{ $item->isFullyReceived() ? 'text-green-600 font-semibold' : '' }}">
                            {{ $item->received_quantity }}
                        </td>
                        <td class="px-4 py-3 text-center">{{ $item->remaining_quantity }}</td>
                        <td class="px-4 py-3 text-right">₹{{ number_format($item->mrp, 2) }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($item->batch_number)
                                <span class="font-mono">{{ $item->batch_number }}</span><br>
                            @endif
                            @if($item->expiry_date)
                                <span class="text-gray-500">Exp: {{ $item->expiry_date->format('d M Y') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- GRN History -->
        @if($purchase->grns->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Goods Receipt History</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GRN #</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Received Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Received By</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchase->grns as $grn)
                    <tr>
                        <td class="px-4 py-3 font-mono">{{ $grn->grn_number }}</td>
                        <td class="px-4 py-3">{{ $grn->received_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $grn->receiver->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $grn->notes ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
