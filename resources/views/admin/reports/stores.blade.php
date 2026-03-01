@extends('layouts.app')

@section('title', 'Store Performance Report')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('admin.reports.stores') }}" class="flex gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                        <a href="{{ route('admin.reports.stores') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Performance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Store Performance</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Store</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($reports as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ optional($item->store)->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ optional($item->store)->email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $item->total_orders }}</td>
                                <td class="px-6 py-4">₹{{ number_format($item->total_sales, 2) }}</td>
                                <td class="px-6 py-4">₹{{ number_format(0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex gap-4 mt-6">
                <a href="{{ route('admin.reports.stores.export.pdf', request()->query()) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Export PDF</a>
                <a href="{{ route('admin.reports.stores.export.excel', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Export Excel</a>
            </div>
    </div>
</div>
@endsection
