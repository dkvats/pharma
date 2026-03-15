@extends('layouts.app')

@section('title', 'My Referral Sales')

@section('content')
<div class="container mx-auto px-4 py-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Referral Sales</h1>
            <p class="text-sm text-gray-500 mt-1">Products sold by stores via your prescriptions</p>
        </div>
        <a href="{{ route('doctor.dashboard') }}"
           class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Summary Cards (quantity only — no financial data) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4">
            <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-receipt text-blue-500"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Referral Sales</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalSales }}</p>
                <p class="text-xs text-gray-400">All time</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4">
            <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-boxes text-purple-500"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Total Qty Sold</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalQty }}</p>
                <p class="text-xs text-gray-400">Units — all time</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-start gap-4">
            <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-check text-green-500"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide">This Month Qty</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $thisMonthQty }}</p>
                <p class="text-xs text-gray-400">{{ now()->format('F Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-800">Referral Sale Records</h2>
            <span class="text-xs text-gray-400">{{ $sales->total() }} record(s) found</span>
        </div>

        @if($sales->isEmpty())
            <div class="p-10 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No referral sales found</p>
                <p class="text-sm text-gray-400 mt-1">When a store records a sale linked to your prescription, it will appear here.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Store</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($sales as $index => $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $sales->firstItem() + $loop->index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-pills text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-800">
                                            {{ $sale->product->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $sale->quantity }} units
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-store text-gray-300 text-xs"></i>
                                        <span class="text-sm text-gray-700">{{ $sale->store->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->created_at->format('d M Y') }}
                                    <span class="block text-xs text-gray-400">{{ $sale->created_at->format('h:i A') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sales->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $sales->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Privacy Note -->
    <p class="text-xs text-gray-400 mt-4 text-center">
        <i class="fas fa-shield-alt mr-1"></i>
        You are viewing product and quantity information only. Pricing details are not shown.
    </p>

</div>
@endsection
