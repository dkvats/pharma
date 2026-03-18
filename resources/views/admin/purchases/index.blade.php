@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container mx-auto px-4 py-6 space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Purchase Orders</h1>
        <a href="{{ route('admin.purchases.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Create Purchase Order</a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form class="flex flex-wrap gap-4">
            <select name="status" class="px-3 py-2 border rounded-lg">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                <option value="partially_received" {{ request('status') == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                <option value="fully_received" {{ request('status') == 'fully_received' ? 'selected' : '' }}>Fully Received</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <select name="supplier" class="px-3 py-2 border rounded-lg">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Order #</th>
                    <th class="px-4 py-3 text-left">Supplier</th>
                    <th class="px-4 py-3 text-left">Order Date</th>
                    <th class="px-4 py-3 text-center">Items</th>
                    <th class="px-4 py-3 text-center">Received</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($purchaseOrders as $po)
                <tr>
                    <td class="px-4 py-3 font-mono font-semibold">{{ $po->order_number }}</td>
                    <td class="px-4 py-3">{{ $po->supplier->name }}</td>
                    <td class="px-4 py-3">{{ $po->order_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-center">{{ $po->total_quantity }}</td>
                    <td class="px-4 py-3 text-center">{{ $po->total_received_quantity }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'ordered' => 'bg-blue-100 text-blue-800',
                                'partially_received' => 'bg-yellow-100 text-yellow-800',
                                'fully_received' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusColors[$po->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.purchases.show', $po) }}" class="text-blue-600 hover:text-blue-800">View</a>
                        @if(!in_array($po->status, ['fully_received', 'cancelled']))
                            <a href="{{ route('admin.purchases.receive.form', $po) }}" class="text-green-600 hover:text-green-800">Receive</a>
                        @endif
                        @if(!in_array($po->status, ['fully_received', 'cancelled']))
                            <form class="inline" method="POST" action="{{ route('admin.purchases.cancel', $po) }}" onsubmit="return confirm('Cancel this purchase order?')">
                                @csrf @method('PATCH')
                                <button class="text-red-600 hover:text-red-800">Cancel</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No purchase orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t">{{ $purchaseOrders->links() }}</div>
    </div>
</div>
@endsection
