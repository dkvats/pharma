@extends('layouts.app')

@section('title', 'Monthly Report')
@section('page-title', 'Monthly Activity Report')

@section('content')
@php
    $month = isset($report['month']) ? \Carbon\Carbon::parse($report['month']) : now();
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Monthly Activity Report</h2>
            <p class="text-gray-600">{{ $month->format('F Y') }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('mr.reports.monthly') }}" method="GET" class="flex gap-2">
                <input type="month" name="month" value="{{ $month->format('Y-m') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="this.form.submit()">
            </form>
            <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Print
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Total Visits</div>
            <div class="text-2xl font-bold text-blue-600">{{ $report['total_visits'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">New Doctors</div>
            <div class="text-2xl font-bold text-green-600">{{ $report['new_doctors'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Total Orders</div>
            <div class="text-2xl font-bold text-purple-600">{{ $report['total_orders'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Total Value</div>
            <div class="text-2xl font-bold text-orange-600">₹{{ number_format($report['total_value'] ?? 0, 0) }}</div>
        </div>
    </div>

    <!-- Weekly Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Weekly Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Week</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Samples</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($report['weekly_breakdown'] ?? [] as $week)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $week['week'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $week['visits'] ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $week['orders'] ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($week['value'] ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $week['samples'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No data available for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
