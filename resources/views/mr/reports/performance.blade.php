@extends('layouts.app')

@section('title', 'Performance Report')
@section('page-title', 'My Performance')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Performance</h2>
            <p class="text-gray-600">Track your achievements and targets</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
            Print
        </button>
    </div>

    <!-- Performance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Monthly Target</div>
            <div class="text-2xl font-bold text-blue-600">₹{{ number_format($report['monthly_target'] ?? 0, 0) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Achieved</div>
            <div class="text-2xl font-bold text-green-600">₹{{ number_format($report['monthly_achieved'] ?? 0, 0) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Achievement %</div>
            <div class="text-2xl font-bold {{ ($report['achievement_percentage'] ?? 0) >= 100 ? 'text-green-600' : 'text-orange-600' }}">
                {{ number_format($report['achievement_percentage'] ?? 0, 1) }}%
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-gray-500 text-sm">Rank</div>
            <div class="text-2xl font-bold text-purple-600">#{{ $report['rank'] ?? '-' }}</div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Monthly Progress</h3>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" 
                 style="width: {{ min($report['achievement_percentage'] ?? 0, 100) }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm text-gray-600">
            <span>0%</span>
            <span>50%</span>
            <span>100%</span>
        </div>
    </div>

    <!-- Recent Achievements -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Recent Achievements</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Achievement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($report['recent_achievements'] ?? [] as $achievement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ isset($achievement['date']) ? \Carbon\Carbon::parse($achievement['date'])->format('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $achievement['description'] ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₹{{ number_format($achievement['value'] ?? 0, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">No recent achievements.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comparison Chart Placeholder -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Performance Trend</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
            <div class="text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p>Performance chart will be displayed here</p>
            </div>
        </div>
    </div>
</div>
@endsection
