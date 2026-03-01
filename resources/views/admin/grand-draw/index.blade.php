@extends('layouts.app')

@section('title', 'Grand Lucky Draw Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Grand Lucky Draw Management</h1>
        <a href="{{ route('admin.grand-draw.history') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
            View History
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('winner'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
            <p class="font-bold">🏆 Winner Announced!</p>
            <p>Doctor: {{ session('winner')->doctor->name }}</p>
            <p>Email: {{ session('winner')->doctor->email }}</p>
        </div>
    @endif

    <!-- Year Selection -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.grand-draw.index') }}" class="flex items-center gap-4">
            <label class="font-semibold text-gray-700">Select Year:</label>
            <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Total Eligible Doctors</p>
            <p class="text-3xl font-bold text-blue-600">{{ $statistics['total_eligible'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Min. 12 spins required</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Draw Status</p>
            <p class="text-3xl font-bold {{ $statistics['draw_run'] ? 'text-green-600' : 'text-yellow-600' }}">
                {{ $statistics['draw_run'] ? 'Completed' : 'Pending' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">{{ $year }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Winner</p>
            @if($statistics['winner'])
                <p class="text-xl font-bold text-purple-600">{{ $statistics['winner']->doctor->name }}</p>
                <p class="text-xs text-gray-400 mt-1">Drawn on {{ $statistics['winner']->draw_date->format('M d, Y') }}</p>
            @else
                <p class="text-xl font-bold text-gray-400">Not drawn yet</p>
            @endif
        </div>
    </div>

    <!-- Run Draw Button -->
    @if(!$statistics['draw_run'] && $statistics['total_eligible'] > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-6 text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ready to Run Grand Lucky Draw?</h3>
            <p class="text-gray-600 mb-4">
                This will randomly select one winner from {{ $statistics['total_eligible'] }} eligible doctors.
                <br>This action cannot be undone.
            </p>
            <form method="POST" action="{{ route('admin.grand-draw.run') }}" onsubmit="return confirm('Are you sure you want to run the Grand Lucky Draw for {{ $year }}?');">
                @csrf
                <input type="hidden" name="year" value="{{ $year }}">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold py-4 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition">
                    🎰 Run Grand Lucky Draw
                </button>
            </form>
        </div>
    @elseif($statistics['draw_run'])
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6 text-center">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Grand Draw Completed for {{ $year }}</h3>
            <p class="text-green-700">
                Winner: <strong>{{ $statistics['winner']->doctor->name }}</strong> ({{ $statistics['winner']->doctor->email }})
            </p>
            <p class="text-sm text-green-600 mt-1">
                Drawn by {{ $statistics['winner']->drawnBy->name }} on {{ $statistics['winner']->draw_date->format('F d, Y at h:i A') }}
            </p>
        </div>
    @elseif($statistics['total_eligible'] === 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6 text-center">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Eligible Doctors</h3>
            <p class="text-yellow-700">
                No doctors have completed 12 or more spins in {{ $year }}.
            </p>
        </div>
    @endif

    <!-- Eligible Doctors List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Eligible Doctors ({{ $statistics['total_eligible'] }})
            </h2>
        </div>
        
        @if($statistics['eligible_doctors']->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p>No eligible doctors found for {{ $year }}.</p>
                <p class="text-sm mt-2">Doctors need at least 12 spins in the year to be eligible.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spins</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($statistics['eligible_doctors'] as $doctor)
                        <tr class="{{ $statistics['winner'] && $statistics['winner']->doctor_id === $doctor->id ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                                @if($statistics['winner'] && $statistics['winner']->doctor_id === $doctor->id)
                                    <span class="text-xs text-green-600 font-bold">🏆 WINNER</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doctor->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                                    {{ $doctor->spin_histories_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($statistics['winner'] && $statistics['winner']->doctor_id === $doctor->id)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Winner</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Eligible</span>
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
