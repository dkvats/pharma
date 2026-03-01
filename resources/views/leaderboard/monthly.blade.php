@extends('layouts.app')

@section('title', 'Monthly Leaderboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">🏆 Monthly Leaderboard</h1>
    <p class="text-gray-600 mb-6">Top performing doctors for {{ now()->format('F Y') }}</p>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('leaderboard.monthly') }}" class="border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium">
                Monthly
            </a>
            <a href="{{ route('leaderboard.all-time') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium">
                All-Time
            </a>
        </nav>
    </div>

    <!-- Leaderboard Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Rank</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referral Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Products Sold</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($rankings as $ranking)
                @php
                    $isCurrentDoctor = $currentDoctorId && $ranking->doctor_id == $currentDoctorId;
                    $isTop3 = $ranking->rank <= 3;
                    $rowClass = $isCurrentDoctor ? 'bg-blue-50' : ($isTop3 ? 'bg-yellow-50' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($ranking->rank == 1)
                            <span class="text-2xl">🥇</span>
                        @elseif($ranking->rank == 2)
                            <span class="text-2xl">🥈</span>
                        @elseif($ranking->rank == 3)
                            <span class="text-2xl">🥉</span>
                        @else
                            <span class="text-gray-900 font-medium">#{{ $ranking->rank }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $ranking->doctor?->name ?? 'Unknown' }}
                                @if($isCurrentDoctor)
                                    <span class="ml-2 text-xs text-blue-600">(You)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $ranking->doctor?->unique_code ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($ranking->tier)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ranking->tier['bg_class'] }}">
                                {{ $ranking->tier['badge'] }} {{ $ranking->tier['name'] }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                        {{ $ranking->total_products }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No rankings available for this month.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $rankings->links() }}
    </div>

    <!-- Legend -->
    <div class="mt-6 bg-gray-50 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Tier Legend</h3>
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="px-2 py-1 rounded-full bg-purple-100 text-purple-800">💎 Elite (300+)</span>
            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-800">🥇 Platinum (150+)</span>
            <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">🥈 Gold (75+)</span>
            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600">🥉 Silver (30+)</span>
            <span class="px-2 py-1 rounded-full bg-orange-100 text-orange-800">🏅 Bronze (0-29)</span>
        </div>
    </div>
</div>
@endsection
