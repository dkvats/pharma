@extends('layouts.super-admin')

@section('title', 'Feature Flags')
@section('page-title', 'Feature Flags')
@section('page-subtitle', 'Control feature rollouts and experiments')

@section('content')
<div class="space-y-6">
    <!-- Info -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-flag text-purple-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-purple-800">Feature Flags</h3>
                <p class="text-sm text-purple-700 mt-1">
                    Control the rollout of new features. You can enable/disable features globally or set a rollout percentage for gradual deployment.
                </p>
            </div>
        </div>
    </div>

    <!-- Feature Flags List -->
    <form method="POST" action="{{ route('super-admin.feature-flags.update') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">All Feature Flags</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage feature availability and rollout</p>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($flags as $flag)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $flag->is_enabled ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i class="fas fa-flag {{ $flag->is_enabled ? 'text-green-600' : 'text-gray-400' }} text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $flag->feature_name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $flag->description }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Key: <code class="bg-gray-100 px-1 rounded">{{ $flag->flag_key }}</code>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <!-- Rollout Percentage -->
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm text-gray-600">Rollout:</label>
                                    <input type="number" name="flags[{{ $flag->id }}][rollout_percentage]" value="{{ $flag->rollout_percentage }}" min="0" max="100" class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm">
                                    <span class="text-sm text-gray-500">%</span>
                                </div>
                                
                                <!-- Toggle -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="flags[{{ $flag->id }}][is_enabled]" value="1" class="sr-only peer" {{ $flag->is_enabled ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>

    <!-- Usage Examples -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Usage in Code</h3>
        </div>
        <div class="p-6">
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg text-sm overflow-x-auto"><code>// Check if feature is enabled
if (SystemSettingService::isFeatureEnabled('new_spin_algorithm')) {
    // New feature logic
}

// Check with rollout percentage
$flag = FeatureFlag::where('flag_key', 'beta_rewards')->first();
if ($flag->isEnabledForUser($userId)) {
    // User gets the beta feature
}</code></pre>
        </div>
    </div>
</div>
@endsection
