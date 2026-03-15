@extends('layouts.super-admin')

@section('title', 'Admin Management')
@section('page-title', 'Admin Users')
@section('page-subtitle', 'Manage administrator accounts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Admin Users</h3>
            <p class="text-sm text-gray-500">Create, edit, and manage administrator accounts</p>
        </div>
        <a href="{{ route('super-admin.admins.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>
            Add Admin
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Admins</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Super Admins</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['super_admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Admins</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['admins'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-cog text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Admins List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($admins as $admin)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600">{{ strtoupper(substr($admin->name, 0, 2)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $admin->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $admin->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($admin->hasRole('Super Admin')) bg-purple-100 text-purple-800
                                @elseif($admin->hasRole('Admin')) bg-red-100 text-red-800
                                @elseif($admin->hasRole('Sub Admin')) bg-orange-100 text-orange-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $admin->roles->first()->name ?? 'No Role' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $admin->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($admin->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('super-admin.admins.edit', $admin) }}" class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if(!$admin->hasRole('Super Admin') || $admins->where('roles.name', 'Super Admin')->count() > 1)
                                <form method="POST" action="{{ route('super-admin.admins.toggle-status', $admin) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        <i class="fas fa-toggle-{{ $admin->status === 'active' ? 'on' : 'off' }}"></i>
                                        {{ $admin->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.admins.reset-password', $admin) }}" class="inline" onsubmit="return confirm('Reset password to default?')">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-key"></i> Reset PW
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.admins.destroy', $admin) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $admins->links() }}
</div>
@endsection
