@extends('layouts.super-admin')

@section('title', 'Module Manager')
@section('page-title', 'Module Manager')
@section('page-subtitle', 'Enable or disable platform modules')

@section('content')
<div class="space-y-6">
    <!-- Module Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Modules</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $modules->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-puzzle-piece text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Enabled</p>
                    <p class="text-2xl font-bold text-green-600">{{ $modules->where('status', 'enabled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Disabled</p>
                    <p class="text-2xl font-bold text-red-600">{{ $modules->where('status', 'disabled')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">All Modules</h3>
            <p class="text-sm text-gray-500 mt-1">Enable or disable system modules</p>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($modules as $module)
                <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $module->status === 'enabled' ? 'bg-green-100' : 'bg-gray-100' }}">
                            <i class="fas fa-puzzle-piece {{ $module->status === 'enabled' ? 'text-green-600' : 'text-gray-400' }} text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $module->module_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $module->description }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                Slug: <code class="bg-gray-100 px-1 rounded">{{ $module->slug }}</code>
                                | Last updated: {{ $module->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $module->status === 'enabled' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($module->status) }}
                        </span>
                        <form method="POST" action="{{ route('super-admin.modules.toggle', $module) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                                <i class="fas {{ $module->status === 'enabled' ? 'fa-toggle-on text-green-600' : 'fa-toggle-off text-gray-400' }} mr-2"></i>
                                {{ $module->status === 'enabled' ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Warning Notice -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notice</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    Disabling a module will make all its features unavailable to users. Some modules may have dependencies on others.
                    Changes take effect immediately.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
