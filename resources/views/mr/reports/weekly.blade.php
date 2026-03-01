@extends('layouts.app')

@section('title', 'Weekly Report')
@section('page-title', 'Weekly Activity Report')

@section('content')
@php
    $weekStart = isset($report['week_start']) ? \Carbon\Carbon::parse($report['week_start']) : now()->startOfWeek();
    $weekEnd = isset($report['week_end']) ? \Carbon\Carbon::parse($report['week_end']) : now()->endOfWeek();
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Weekly Activity Report</h2>
            <p class="text-gray-600">{{ $weekStart->format('d M Y') }} - {{ $weekEnd->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('mr.reports.weekly') }}" method="GET" class="flex gap-2">
                <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"
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
            <div class="text-gray-500 text-sm">Doctors Visited</div>
            <div class="text-2xl font-bold text-blue-600">{{ $report['doctors_visited'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">New Doctors</div>
            <div class="text-2xl font-bold text-green-600">{{ $report['new_doctors'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Orders Placed</div>
            <div class="text-2xl font-bold text-purple-600">{{ $report['orders_placed'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Order Value</div>
            <div class="text-2xl font-bold text-orange-600">₹{{ number_format($report['order_value'] ?? 0, 0) }}</div>
        </div>
    </div>

    <!-- Daily Breakdown -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Daily Breakdown</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visits</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($report['daily_breakdown'] ?? [] as $day)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $day['day'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $day['date'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $day['visits'] ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $day['orders'] ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($day['value'] ?? 0, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No data available for this week.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
