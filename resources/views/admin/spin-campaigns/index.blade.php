@extends('layouts.app')

@section('title', 'Spin Campaigns')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Spin Campaigns</h1>
    <p class="text-gray-600 mb-6">Create global campaigns where ALL eligible doctors win the same reward during a specific time period.</p>

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

    <!-- Create Campaign Section -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Create New Campaign</h2>
        <p class="text-sm text-gray-600 mb-4">During campaign period, ALL doctors who spin will win this reward (Priority 2 in spin logic).</p>

        <form action="{{ route('admin.spin-campaigns.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="reward_id" class="block text-sm font-medium text-gray-700 mb-1">Select Reward *</label>
                <select name="reward_id" id="reward_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose Reward...</option>
                    @foreach($rewards as $reward)
                        <option value="{{ $reward->id }}">
                            {{ $reward->name }} 
                            @if($reward->value > 0)
                                (₹{{ $reward->value }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                <input type="datetime-local" name="starts_at" id="starts_at" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                <input type="datetime-local" name="ends_at" id="ends_at" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                    Create Campaign
                </button>
            </div>
        </form>
    </div>

    <!-- Active Campaign -->
    @if($activeCampaign)
    <div class="bg-green-50 border border-green-200 rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-green-800">🎯 Active Campaign</h2>
                <p class="text-green-700 mt-1">
                    <strong>{{ $activeCampaign->reward->name }}</strong> is active for all spins
                </p>
                <p class="text-sm text-green-600 mt-1">
                    From {{ $activeCampaign->starts_at->format('M d, Y H:i') }} 
                    to {{ $activeCampaign->ends_at->format('M d, Y H:i') }}
                </p>
            </div>
            <form action="{{ route('admin.spin-campaigns.destroy', $activeCampaign) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 font-medium"
                    onclick="return confirm('Are you sure you want to end this campaign?')">
                    End Campaign
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Upcoming Campaigns -->
    @if($upcomingCampaigns->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Upcoming Campaigns</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reward</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Starts At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ends At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($upcomingCampaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $campaign->reward->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $campaign->starts_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $campaign->ends_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('admin.spin-campaigns.destroy', $campaign) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Cancel this campaign?')">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Past Campaigns -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Campaign History</h2>
        </div>
        @if($pastCampaigns->isEmpty())
            <div class="p-6 text-center text-gray-500">
                No past campaigns.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reward</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pastCampaigns as $campaign)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->reward->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $campaign->starts_at->format('M d, Y') }} - {{ $campaign->ends_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $campaign->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $campaign->is_active ? 'Completed' : 'Cancelled' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pastCampaigns->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
