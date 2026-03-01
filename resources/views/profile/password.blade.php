@extends('layouts.app')

@section('title', 'Change Password')
@section('page-title', 'Change Password')
@section('page-description', 'Update your account password')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-card border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                    <i data-lucide="lock" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Change Password</h2>
                    <p class="text-primary-100 text-sm">Keep your account secure</p>
                </div>
            </div>
        </div>

        <!-- Password Form -->
        <form action="{{ route('profile.password.update') }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    Current Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="key" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="current_password" id="current_password" required
                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('current_password') border-red-500 @enderror">
                    <button type="button" onclick="togglePassword('current_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i data-lucide="eye" class="w-5 h-5 text-gray-400 hover:text-gray-600 cursor-pointer" id="current_password_icon"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="password" id="password" required
                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror">
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i data-lucide="eye" class="w-5 h-5 text-gray-400 hover:text-gray-600 cursor-pointer" id="password_icon"></i>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm New Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="check-circle" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <i data-lucide="eye" class="w-5 h-5 text-gray-400 hover:text-gray-600 cursor-pointer" id="password_confirmation_icon"></i>
                    </button>
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="bg-blue-50 rounded-xl p-4">
                <h3 class="text-sm font-medium text-blue-900 mb-2 flex items-center">
                    <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                    Password Requirements
                </h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 mr-2"></i> At least 8 characters</li>
                    <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 mr-2"></i> Mix of letters and numbers</li>
                    <li class="flex items-center"><i data-lucide="check" class="w-4 h-4 mr-2"></i> At least one special character</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Back to Profile
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 flex items-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        field.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}
</script>
@endsection
