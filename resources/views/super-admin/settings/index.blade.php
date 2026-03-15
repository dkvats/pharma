@extends('layouts.super-admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')
@section('page-subtitle', 'Configure global platform settings')

@section('content')
<div class="space-y-6">
    <!-- Settings Form -->
    <form method="POST" action="{{ route('super-admin.settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Core System Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Core System Settings</h3>
                <p class="text-sm text-gray-500 mt-1">Control fundamental system behavior and features</p>
            </div>
            <div class="p-6 space-y-6">
                @foreach($settings as $setting)
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700">{{ $setting->description }}</label>
                            <p class="text-xs text-gray-500 mt-1">Key: <code class="bg-gray-100 px-1 rounded">{{ $setting->key }}</code></p>
                        </div>
                        <div class="ml-4">
                            @if($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[{{ $setting->id }}]" value="1" class="sr-only peer" {{ $setting->value === '1' || $setting->value === 'true' ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                </label>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                            @elseif($setting->type === 'json')
                                <textarea name="settings[{{ $setting->id }}]" rows="3" class="w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm font-mono">{{ $setting->value }}</textarea>
                            @else
                                <input type="text" name="settings[{{ $setting->id }}]" value="{{ $setting->value }}" class="w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-sm">
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-save mr-2"></i>
                Save Settings
            </button>
        </div>
    </form>

    <!-- Quick Toggle Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Quick Toggle Actions</h3>
            <p class="text-sm text-gray-500 mt-1">Quickly enable or disable major features</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Spin System -->
                <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">Spin System</p>
                        <p class="text-xs text-gray-500">Enable/disable spin wheel</p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.settings.toggle') }}">
                        @csrf
                        <input type="hidden" name="key" value="spin_enabled">
                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium {{ App\Services\SystemSettingService::isEnabled('spin_enabled') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ App\Services\SystemSettingService::isEnabled('spin_enabled') ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>

                <!-- Offers System -->
                <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">Offers System</p>
                        <p class="text-xs text-gray-500">Enable/disable offers</p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.settings.toggle') }}">
                        @csrf
                        <input type="hidden" name="key" value="offers_enabled">
                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium {{ App\Services\SystemSettingService::isEnabled('offers_enabled') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ App\Services\SystemSettingService::isEnabled('offers_enabled') ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>

                <!-- MR Module -->
                <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">MR Module</p>
                        <p class="text-xs text-gray-500">Enable/disable MR features</p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.settings.toggle') }}">
                        @csrf
                        <input type="hidden" name="key" value="mr_module_enabled">
                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium {{ App\Services\SystemSettingService::isEnabled('mr_module_enabled') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ App\Services\SystemSettingService::isEnabled('mr_module_enabled') ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>

                <!-- Order Auto Approve -->
                <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">Auto Approve Orders</p>
                        <p class="text-xs text-gray-500">Automatic order approval</p>
                    </div>
                    <form method="POST" action="{{ route('super-admin.settings.toggle') }}">
                        @csrf
                        <input type="hidden" name="key" value="order_auto_approve">
                        <button type="submit" class="px-3 py-1 rounded-full text-xs font-medium {{ App\Services\SystemSettingService::isEnabled('order_auto_approve') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ App\Services\SystemSettingService::isEnabled('order_auto_approve') ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
