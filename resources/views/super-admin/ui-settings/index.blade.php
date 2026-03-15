@extends('layouts.super-admin')

@section('title', 'UI Settings')
@section('page-title', 'UI Settings')
@section('page-subtitle', 'Customize interface appearance')

@section('content')
<div class="space-y-6">
    <form method="POST" action="{{ route('super-admin.ui-settings.update') }}">
        @csrf
        @method('PUT')

        <!-- Branding Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Branding Settings</h3>
                <p class="text-sm text-gray-500 mt-1">Customize the look and feel of your platform</p>
            </div>
            <div class="p-6 space-y-6">
                <!-- Site Logo -->
                <div>
                    <label for="site_logo" class="block text-sm font-medium text-gray-700 mb-1">Site Logo URL</label>
                    <input type="text" name="settings[site_logo]" id="site_logo" value="{{ $settings->get('site_logo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" placeholder="/images/logo.png">
                    <p class="mt-1 text-xs text-gray-500">Enter the URL path to your logo image</p>
                </div>

                <!-- Site Name -->
                <div>
                    <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                    <input type="text" name="settings[site_name]" id="site_name" value="{{ $settings->get('site_name', 'Pharma ERP') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Primary Color -->
                <div>
                    <label for="site_primary_color" class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="color_picker_primary" id="color_picker_primary" value="{{ $settings->get('site_primary_color', '#3b82f6') }}" class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                        <input type="text" name="settings[site_primary_color]" id="site_primary_color" value="{{ $settings->get('site_primary_color', '#3b82f6') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <!-- Secondary Color -->
                <div>
                    <label for="site_secondary_color" class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" name="color_picker_secondary" id="color_picker_secondary" value="{{ $settings->get('site_secondary_color', '#1e40af') }}" class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                        <input type="text" name="settings[site_secondary_color]" id="site_secondary_color" value="{{ $settings->get('site_secondary_color', '#1e40af') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Theme -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Dashboard Theme</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="dashboard_theme" class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                    <select name="settings[dashboard_theme]" id="dashboard_theme" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="light" {{ $settings->get('dashboard_theme') === 'light' ? 'selected' : '' }}>Light</option>
                        <option value="dark" {{ $settings->get('dashboard_theme') === 'dark' ? 'selected' : '' }}>Dark</option>
                        <option value="auto" {{ $settings->get('dashboard_theme') === 'auto' ? 'selected' : '' }}>Auto (System)</option>
                    </select>
                </div>

                <div>
                    <label for="sidebar_style" class="block text-sm font-medium text-gray-700 mb-1">Sidebar Style</label>
                    <select name="settings[sidebar_style]" id="sidebar_style" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="dark" {{ $settings->get('sidebar_style') === 'dark' ? 'selected' : '' }}>Dark</option>
                        <option value="light" {{ $settings->get('sidebar_style') === 'light' ? 'selected' : '' }}>Light</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Footer Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Footer Settings</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-1">Footer Text</label>
                    <textarea name="settings[footer_text]" id="footer_text" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ $settings->get('footer_text', '© 2024 Pharma ERP. All rights reserved.') }}</textarea>
                </div>

                <div>
                    <label for="footer_links" class="block text-sm font-medium text-gray-700 mb-1">Footer Links (JSON)</label>
                    <textarea name="settings[footer_links]" id="footer_links" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ $settings->get('footer_links', '[{"text":"Privacy Policy","url":"/privacy-policy"},{"text":"Terms of Service","url":"/terms"}]') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">JSON array of link objects with "text" and "url" properties</p>
                </div>
            </div>
        </div>

        <!-- Custom CSS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Custom CSS</h3>
            </div>
            <div class="p-6">
                <textarea name="settings[custom_css]" id="custom_css" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ $settings->get('custom_css') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Add custom CSS to override default styles</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                <i class="fas fa-save mr-2"></i>
                Save UI Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Sync color pickers with text inputs
    document.getElementById('color_picker_primary').addEventListener('input', function() {
        document.getElementById('site_primary_color').value = this.value;
    });
    document.getElementById('site_primary_color').addEventListener('input', function() {
        document.getElementById('color_picker_primary').value = this.value;
    });
    document.getElementById('color_picker_secondary').addEventListener('input', function() {
        document.getElementById('site_secondary_color').value = this.value;
    });
    document.getElementById('site_secondary_color').addEventListener('input', function() {
        document.getElementById('color_picker_secondary').value = this.value;
    });
</script>
@endpush
@endsection
