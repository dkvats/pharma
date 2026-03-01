@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                        <a href="{{ route('admin.reports.sales') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Orders</div>
                    <div class="text-2xl font-bold">{{ $summary['total_orders'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Sales</div>
                    <div class="text-2xl font-bold">₹{{ number_format($summary['total_revenue'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Total Commission</div>
                    <div class="text-2xl font-bold">₹{{ number_format($summary['total_commission'], 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-600">Average Order Value</div>
                    <div class="text-2xl font-bold">₹{{ number_format($summary['average_order_value'], 2) }}</div>
                </div>
            </div>

            <!-- Sales by Type -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Sales by Type</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($salesByType as $type => $data)
                            <tr>
                                <td class="px-6 py-4">{{ ucwords(str_replace('_', ' ', $type)) }}</td>
                                <td class="px-6 py-4">{{ $data['count'] }}</td>
                                <td class="px-6 py-4">₹{{ number_format($data['total'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex gap-4 mb-6">
                <a href="{{ route('admin.reports.sales.export.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Export PDF</a>
                <a href="{{ route('admin.reports.sales.export.excel', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Export Excel</a>
            </div>
    </div>
</div>
@endsection
