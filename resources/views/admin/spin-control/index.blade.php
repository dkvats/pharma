@extends('layouts.app')

@section('title', 'Spin Control')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Spin Control</h1>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Assign Reward Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Assign Specific Reward to Doctor</h2>
        <p class="text-sm text-gray-600 mb-4">
            When a doctor spins next, they will receive the assigned reward instead of a random one.
        </p>

        <form action="{{ route('admin.spin-control.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Select Doctor</label>
                <select name="doctor_id" id="doctor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose Doctor...</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }} ({{ $doctor->unique_code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="reward_id" class="block text-sm font-medium text-gray-700 mb-1">Select Reward</label>
                <select name="reward_id" id="reward_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose Reward...</option>
                    @foreach($rewards as $reward)
                        <option value="{{ $reward->id }}">
                            {{ $reward->name }} 
                            @if($reward->value > 0)
                                (₹{{ $reward->value }})
                            @endif
                            @if($reward->stock !== null)
                                [Stock: {{ $reward->stock }}]
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires At (Optional)</label>
                <input type="datetime-local" name="expires_at" id="expires_at"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Assign Reward
                </button>
            </div>
        </form>
    </div>

    <!-- Pending Overrides -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Pending Reward Assignments</h2>
            <p class="text-sm text-gray-600">These doctors will receive the assigned reward on their next spin.</p>
        </div>

        @if($pendingOverrides->isEmpty())
            <div class="p-6 text-center text-gray-500">
                No pending reward assignments.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Reward</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingOverrides as $override)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $override->doctor->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $override->doctor->unique_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $override->reward->name }}</div>
                                    @if($override->reward->value > 0)
                                        <div class="text-sm text-gray-500">₹{{ $override->reward->value }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $override->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($override->expires_at)
                                        <span class="{{ $override->expires_at->isPast() ? 'text-red-600' : 'text-gray-500' }}">
                                            {{ $override->expires_at->format('M d, Y H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="{{ route('admin.spin-control.destroy', $override) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure? This will cancel the reward assignment.')">
                                            Cancel
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pendingOverrides->links() }}
            </div>
        @endif
    </div>

    <!-- Override History -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Assignment History</h2>
            <p class="text-sm text-gray-600">Previously assigned rewards that have been won by doctors.</p>
        </div>

        @if($overrideHistory->isEmpty())
            <div class="p-6 text-center text-gray-500">
                No reward assignments have been used yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reward Won</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Used At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($overrideHistory as $override)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $override->doctor->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $override->doctor->unique_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $override->reward->name }}</div>
                                    @if($override->reward->value > 0)
                                        <div class="text-sm text-gray-500">₹{{ $override->reward->value }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $override->updated_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $overrideHistory->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
