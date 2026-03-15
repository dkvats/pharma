@extends('layouts.super-admin')

@section('title', 'Notification Templates')
@section('page-title', 'Notification Templates')
@section('page-subtitle', 'Manage email notification templates')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Email & Notification Templates</h3>
            <p class="text-sm text-gray-500">Customize notification messages sent to users</p>
        </div>
        <a href="{{ route('super-admin.notifications.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>
            Create Template
        </a>
    </div>

    <!-- Templates List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-yellow-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $template->key }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->type === 'email' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($template->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $template->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $template->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('super-admin.notifications.edit', $template) }}" class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('super-admin.notifications.toggle', $template) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-toggle-{{ $template->is_active ? 'on' : 'off' }}"></i>
                                    {{ $template->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('super-admin.notifications.destroy', $template) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-envelope text-4xl text-gray-300 mb-4"></i>
                            <p>No notification templates created yet.</p>
                            <a href="{{ route('super-admin.notifications.create') }}" class="text-primary-600 hover:text-primary-700">Create your first template</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $templates->links() }}

    <!-- Available Variables -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Available Variables</h3>
            <p class="text-sm text-gray-500 mt-1">Use these variables in your templates. They will be replaced with actual values.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{user_name}</code>
                    <p class="text-gray-500 mt-1">Recipient's full name</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{user_email}</code>
                    <p class="text-gray-500 mt-1">Recipient's email</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{order_id}</code>
                    <p class="text-gray-500 mt-1">Order reference ID</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{order_total}</code>
                    <p class="text-gray-500 mt-1">Total order amount</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{reward_name}</code>
                    <p class="text-gray-500 mt-1">Reward/prize name</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <code class="text-primary-600">{app_name}</code>
                    <p class="text-gray-500 mt-1">Application name</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
