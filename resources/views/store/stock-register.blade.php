@extends('layouts.app')

@section('title', 'Stock Register')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Stock Register</h1>
            <p class="text-sm text-gray-500 mt-1">Track all stock movements with opening and closing balances</p>
        </div>
        <a href="{{ route('store.stock.index') }}"
           class="text-sm text-gray-600 hover:text-gray-800 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Stock
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 rounded-xl border border-green-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-green-600 uppercase font-medium">Total Purchases</p>
                    <p class="text-xl font-bold text-green-700">{{ $summary['total_purchases'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 rounded-xl border border-blue-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-up text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-blue-600 uppercase font-medium">Total Sales</p>
                    <p class="text-xl font-bold text-blue-700">{{ $summary['total_sales'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 rounded-xl border border-amber-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sliders-h text-amber-600"></i>
                </div>
                <div>
                    <p class="text-xs text-amber-600 uppercase font-medium">Adjustments</p>
                    <p class="text-xl font-bold text-amber-700">{{ $summary['total_adjustments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form action="{{ route('store.stock-register') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Product Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Product</label>
                <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Transaction Type Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Type</label>
                <select name="transaction_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="purchase" {{ request('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                    <option value="sale" {{ request('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                    <option value="adjustment" {{ request('transaction_type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">To Date</label>
                <div class="flex gap-2">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(request()->hasAny(['product_id', 'transaction_type', 'date_from', 'date_to']))
            <div class="mt-4 flex justify-end">
                <a href="{{ route('store.stock-register') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <i class="fas fa-times-circle"></i> Clear Filters
                </a>
            </div>
        @endif
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Transaction History</h2>
            <span class="text-xs text-gray-400">{{ $transactions->total() }} record(s)</span>
        </div>

        @if($transactions->isEmpty())
            <div class="p-10 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No transactions found</p>
                <p class="text-sm text-gray-400 mt-1">Stock transactions will appear here when purchases or sales are recorded.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Opening</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Closing</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $transaction->created_at->format('d M Y') }}
                                    <span class="block text-xs text-gray-400">{{ $transaction->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-pills text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ $transaction->product->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $transaction->transaction_type == 'purchase' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $transaction->transaction_type == 'sale' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $transaction->transaction_type == 'adjustment' ? 'bg-amber-100 text-amber-800' : '' }}">
                                        <i class="fas {{ $transaction->transaction_type == 'purchase' ? 'fa-arrow-down' : ($transaction->transaction_type == 'sale' ? 'fa-arrow-up' : 'fa-sliders-h') }} mr-1"></i>
                                        {{ $transaction->type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium
                                    {{ $transaction->transaction_type == 'purchase' ? 'text-green-600' : ($transaction->transaction_type == 'sale' ? 'text-blue-600' : 'text-amber-600') }}">
                                    {{ $transaction->transaction_type == 'purchase' ? '+' : ($transaction->transaction_type == 'sale' ? '-' : '') }}{{ $transaction->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
                                    {{ $transaction->opening_balance }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-800">
                                    {{ $transaction->closing_balance }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-xs text-gray-400">
                                    @if($transaction->reference_type && $transaction->reference_id)
                                        <span class="bg-gray-100 px-2 py-1 rounded">{{ ucfirst($transaction->reference_type) }} #{{ $transaction->reference_id }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 text-xs text-gray-500">
        <div class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-green-100 border border-green-300"></span>
            <span>Purchase: Stock added</span>
        </div>
        <div class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-blue-100 border border-blue-300"></span>
            <span>Sale: Stock sold</span>
        </div>
        <div class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-300"></span>
            <span>Adjustment: Manual correction</span>
        </div>
    </div>

</div>
@endsection
