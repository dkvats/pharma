@extends('layouts.app')

@section('title', 'Batch Inventory Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Batch Inventory</h1>
        <a href="{{ route('admin.inventory-reports.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Reports
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.inventory-reports.batch-inventory') }}" class="flex gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select name="product_id" class="px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('admin.inventory-reports.batch-inventory') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manufacture Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">MRP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($batches as $batch)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $batch->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->batch_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->manufacture_date?->format('d M Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->expiry_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">₹{{ number_format($batch->mrp, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $batch->getExpiryBadgeClass() }}">
                                {{ $batch->getExpiryStatusText() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No batches found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $batches->links() }}
    </div>
</div>
@endsection
