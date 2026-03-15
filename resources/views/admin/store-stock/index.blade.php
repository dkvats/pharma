@extends('layouts.app')

@section('title', 'Store Stock Monitoring')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Store Stock Monitoring</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
            Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.store-stock.index') }}" class="flex flex-wrap gap-4">
            <div>
                <select name="store_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="product_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Filter
            </button>
            <a href="{{ route('admin.store-stock.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                Clear
            </a>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stocks as $stock)
                    <tr class="{{ $stock->available_stock < 10 ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $stock->store->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->store->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $stock->product->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->product->sku ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $stock->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $stock->sold_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold {{ $stock->available_stock <= 0 ? 'text-red-600' : ($stock->available_stock < 10 ? 'text-orange-500' : 'text-green-600') }}">
                                {{ $stock->available_stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.store-stock.show', $stock->store) }}" class="text-blue-600 hover:text-blue-900">
                                View Store Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No stock records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $stocks->links() }}
    </div>
</div>
@endsection
