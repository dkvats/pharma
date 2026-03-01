@extends('layouts.app')

@section('title', 'My Targets')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Monthly Targets</h1>

    <!-- Current Month Progress -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Month Progress</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Target</p>
                <p class="text-2xl font-bold text-blue-600">{{ $currentProgress['target'] }}</p>
                <p class="text-xs text-gray-500">products</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Achieved</p>
                <p class="text-2xl font-bold text-green-600">{{ $currentProgress['current'] }}</p>
                <p class="text-xs text-gray-500">products</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Progress</p>
                <p class="text-2xl font-bold text-purple-600">{{ $currentProgress['percentage'] }}%</p>
                <p class="text-xs text-gray-500">complete</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Status</p>
                @if($currentProgress['already_spun'])
                    <p class="text-lg font-bold text-gray-600">Already Spun</p>
                    <p class="text-xs text-gray-500">Reward claimed</p>
                @elseif($currentProgress['spin_eligible'])
                    <p class="text-lg font-bold text-green-600">Eligible!</p>
                    <p class="text-xs text-gray-500"><a href="{{ route('doctor.spin.index') }}" class="text-blue-600 hover:underline">Go to Spin</a></p>
                @elseif($currentProgress['completed'])
                    <p class="text-lg font-bold text-blue-600">Completed</p>
                    <p class="text-xs text-gray-500">Target achieved</p>
                @else
                    <p class="text-lg font-bold text-orange-600">In Progress</p>
                    <p class="text-xs text-gray-500">Keep going!</p>
                @endif
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: {{ $currentProgress['percentage'] }}%"></div>
        </div>
        <p class="text-sm text-gray-600 text-center">
            {{ $currentProgress['current'] }} of {{ $currentProgress['target'] }} products sold
        </p>
    </div>

    <!-- Target History -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Target History (Last 12 Months)</h2>
        </div>
        
        @if($targetHistory->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p>No target history available yet.</p>
                <p class="text-sm mt-2">Start placing orders to build your history!</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Achieved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($targetHistory as $target)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('F Y', mktime(0, 0, 0, $target->month, 1, $target->year)) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $target->target_quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $target->achieved_quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $percentage = $target->target_quantity > 0 
                                        ? round(($target->achieved_quantity / $target->target_quantity) * 100) 
                                        : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2 w-24">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $percentage }}%</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($target->spin_completed_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Reward Claimed
                                    </span>
                                @elseif($target->target_completed)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        In Progress
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
