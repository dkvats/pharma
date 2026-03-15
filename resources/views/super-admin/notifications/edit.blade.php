@extends('layouts.super-admin')

@section('title', 'Edit Notification Template')
@section('page-title', 'Edit Notification Template')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.notifications.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Templates
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Edit: {{ $notification->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">Key: {{ $notification->key }}</p>
        </div>
        <form method="POST" action="{{ route('super-admin.notifications.update', $notification) }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $notification->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" id="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="email" {{ $notification->type === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ $notification->type === 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="push" {{ $notification->type === 'push' ? 'selected' : '' }}>Push Notification</option>
                            <option value="in_app" {{ $notification->type === 'in_app' ? 'selected' : '' }}>In-App Notification</option>
                        </select>
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject (Email)</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $notification->subject) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Body -->
                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Message Body</label>
                    <textarea name="body" id="body" rows="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm" required>{{ old('body', $notification->body) }}</textarea>
                </div>

                <!-- Variables -->
                <div>
                    <label for="variables" class="block text-sm font-medium text-gray-700 mb-1">Available Variables (JSON)</label>
                    <textarea name="variables" id="variables" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">{{ old('variables', $notification->variables) }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $notification->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('super-admin.notifications.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Update Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
