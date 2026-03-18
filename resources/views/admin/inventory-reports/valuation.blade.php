@extends('layouts.app')

@section('title', 'Stock Valuation Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Stock Valuation</h1>
        <a href="{{ route('admin.inventory-reports.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Reports
        </a>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Inventory Value</p>
                <p class="text-4xl font-bold">₹{{ number_format($totalValue, 2) }}</p>
            </div>
            <div class="p-4 bg-white bg-opacity-20 rounded-full">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch Count</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Quantity</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    @if($productValues[$product->id] > 0)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $product->category }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->sku ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->batches->count() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->batches->sum('quantity') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">₹{{ number_format($productValues[$product->id], 2) }}</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No products with stock found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-800">Total Value:</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800">₹{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
