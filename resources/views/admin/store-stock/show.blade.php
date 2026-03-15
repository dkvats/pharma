@extends('layouts.app')

@section('title', 'Store Stock Details — ' . $store->name)

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Stock Details: {{ $store->name }}</h1>
        <a href="{{ route('admin.store-stock.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
            &larr; Back to Store Stocks
        </a>
    </div>

    <!-- Store Info Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Store Name</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $store->name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                <p class="text-sm text-gray-700 mt-1">{{ $store->email ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</p>
                <p class="text-sm text-gray-700 mt-1">{{ $store->phone ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Low Stock Warning -->
    @php $lowStockCount = $stocks->filter(fn($s) => $s->available_stock < 10)->count(); @endphp
    @if($lowStockCount > 0)
        <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg px-4 py-3 mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span><strong>{{ $lowStockCount }} product(s)</strong> are running low on stock (available &lt; 10). Consider contacting this store.</span>
        </div>
    @endif

    <!-- Stock Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stocks as $stock)
                    <tr class="{{ $stock->available_stock < 10 ? 'bg-red-50' : '' }}">
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
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($stock->available_stock <= 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Out of Stock</span>
                            @elseif($stock->available_stock < 10)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">In Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No stock records found for this store.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
