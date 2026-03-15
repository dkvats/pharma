@extends('layouts.super-admin')

@section('title', 'Create Notification Template')
@section('page-title', 'Create Notification Template')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.notifications.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Templates
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">New Notification Template</h3>
        </div>
        <form method="POST" action="{{ route('super-admin.notifications.store') }}">
            @csrf
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>

                    <!-- Key -->
                    <div>
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-1">Template Key</label>
                        <input type="text" name="key" id="key" value="{{ old('key') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" placeholder="order_placed" required>
                        <p class="mt-1 text-xs text-gray-500">Unique identifier (lowercase, underscores)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="email">Email</option>
                            <option value="sms">SMS</option>
                            <option value="push">Push Notification</option>
                            <option value="in_app">In-App Notification</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Subject (for emails) -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject (Email)</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" placeholder="Your order has been placed">
                </div>

                <!-- Body -->
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
                    <textarea name="body" id="body" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm" required>{{ old('body') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Use {variable_name} for dynamic content. Example: Hello {user_name}, your order {order_id} has been received.</p>
                </div>

                <!-- Variables -->
                <div>
                    <label for="variables" class="block text-sm font-medium text-gray-700 mb-1">Available Variables (JSON)</label>
                    <textarea name="variables" id="variables" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ old('variables', '["user_name", "user_email", "order_id"]') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">JSON array of variable names supported by this template</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('super-admin.notifications.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Create Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
