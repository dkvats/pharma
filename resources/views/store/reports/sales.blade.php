@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Sales Report</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-green-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Units Sold</p>
            <p class="text-2xl font-bold text-green-600">{{ $totalUnits }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-4">
            <p class="text-sm text-gray-600">Total Revenue</p>
            <p class="text-2xl font-bold text-blue-600">₹{{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Sales Details</h2>
        </div>

        @if($sales->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p>No sales found in this period.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sales as $sale)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $sale->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
