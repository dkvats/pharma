@extends('layouts.super-admin')

@section('title', 'Edit Admin User')
@section('page-title', 'Edit Admin User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.admins.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Admins
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Edit: {{ $user->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $user->email }}</p>
        </div>
        <form method="POST" action="{{ route('super-admin.admins.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                            <option value="Super Admin" {{ $user->hasRole('Super Admin') ? 'selected' : '' }}>Super Admin</option>
                            <option value="Admin" {{ $user->hasRole('Admin') ? 'selected' : '' }}>Admin</option>
                            <option value="Sub Admin" {{ $user->hasRole('Sub Admin') ? 'selected' : '' }}>Sub Admin</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Employee Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $user->code) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-gray-100" readonly>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" id="address" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('address', $user->address) }}</textarea>
                </div>

                <!-- New Password (optional) -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Change Password (optional)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('super-admin.admins.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Update Admin</button>
            </div>
        </form>
    </div>
</div>
@endsection
