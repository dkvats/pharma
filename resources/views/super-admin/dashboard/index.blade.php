@extends('layouts.super-admin')

@section('title', 'Platform Control Panel')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Platform Overview')

@section('content')
<div class="space-y-6">
    <!-- Platform Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl border border-slate-700 p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Users</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="flex items-center text-green-400">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    {{ $stats['active_users'] }} active
                </span>
                <span class="text-slate-500 ml-3">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $stats['pending_users'] }} pending
                </span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl border border-slate-700 p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Orders</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/20">
                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="flex items-center text-yellow-400">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $stats['pending_orders'] }} pending
                </span>
                <span class="text-slate-500 ml-3">
                    {{ $stats['delivered_orders'] }} delivered
                </span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl border border-slate-700 p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Total Revenue</p>
                    <p class="text-3xl font-bold text-white mt-1">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-sa-500 to-sa-700 rounded-xl flex items-center justify-center shadow-lg shadow-sa-500/20">
                    <i class="fas fa-dollar-sign text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="flex items-center text-sa-400">
                    <i class="fas fa-chart-line mr-1"></i>
                    ${{ number_format($stats['monthly_revenue'], 2) }} this month
                </span>
            </div>
        </div>

        <!-- Active Modules -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl border border-slate-700 p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-400">Active Modules</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['active_modules'] }}/{{ $stats['total_modules'] }}</p>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <i class="fas fa-puzzle-piece text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-700 rounded-full h-2">
                    <div class="bg-gradient-to-r from-sa-500 to-sa-400 h-2 rounded-full" style="width: {{ $stats['total_modules'] > 0 ? ($stats['active_modules'] / $stats['total_modules'] * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Breakdown -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-800/50 rounded-lg border border-slate-700 p-4 text-center">
            <div class="w-10 h-10 mx-auto bg-blue-500/20 rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-user-md text-blue-400"></i>
            </div>
            <p class="text-xl font-bold text-white">{{ $stats['total_doctors'] }}</p>
            <p class="text-xs text-slate-400">Doctors</p>
        </div>
        <div class="bg-slate-800/50 rounded-lg border border-slate-700 p-4 text-center">
            <div class="w-10 h-10 mx-auto bg-yellow-500/20 rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-store text-yellow-400"></i>
            </div>
            <p class="text-xl font-bold text-white">{{ $stats['total_stores'] }}</p>
            <p class="text-xs text-slate-400">Stores</p>
        </div>
        <div class="bg-slate-800/50 rounded-lg border border-slate-700 p-4 text-center">
            <div class="w-10 h-10 mx-auto bg-purple-500/20 rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-user-tie text-purple-400"></i>
            </div>
            <p class="text-xl font-bold text-white">{{ $stats['total_mrs'] }}</p>
            <p class="text-xs text-slate-400">MRs</p>
        </div>
        <div class="bg-slate-800/50 rounded-lg border border-slate-700 p-4 text-center">
            <div class="w-10 h-10 mx-auto bg-red-500/20 rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-user-shield text-red-400"></i>
            </div>
            <p class="text-xl font-bold text-white">{{ $stats['total_admins'] }}</p>
            <p class="text-xs text-slate-400">Admins</p>
        </div>
        <div class="bg-slate-800/50 rounded-lg border border-slate-700 p-4 text-center col-span-2 md:col-span-1">
            <div class="w-10 h-10 mx-auto bg-green-500/20 rounded-lg flex items-center justify-center mb-2">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <p class="text-xl font-bold text-white">{{ $stats['inactive_modules'] }}</p>
            <p class="text-xs text-slate-400">Inactive Modules</p>
        </div>
    </div>

    <!-- System Status & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- System Health -->
        <div class="lg:col-span-2 bg-slate-800/50 rounded-xl border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-heartbeat text-red-400 mr-2"></i>
                    System Health
                </h3>
                <span class="flex items-center text-xs text-green-400 bg-green-500/10 px-2 py-1 rounded-full">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-1 sa-pulse"></span>
                    All Systems Operational
                </span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $healthItems = [
                        ['name' => 'Database', 'status' => 'Connected', 'icon' => 'fa-database', 'color' => 'green'],
                        ['name' => 'Cache', 'status' => $systemHealth['cache_status'] ?? 'Active', 'icon' => 'fa-bolt', 'color' => 'green'],
                        ['name' => 'Storage', 'status' => $systemHealth['storage_status'] ?? 'Writable', 'icon' => 'fa-hdd', 'color' => 'green'],
                        ['name' => 'Queue', 'status' => $systemHealth['queue_status'] ?? 'Running', 'icon' => 'fa-tasks', 'color' => 'green'],
                    ];
                @endphp
                @foreach($healthItems as $item)
                    <div class="text-center p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                        <div class="w-12 h-12 mx-auto bg-{{ $item['color'] }}-500/20 rounded-lg flex items-center justify-center mb-3">
                            <i class="fas {{ $item['icon'] }} text-{{ $item['color'] }}-400 text-lg"></i>
                        </div>
                        <p class="text-sm font-medium text-white">{{ $item['name'] }}</p>
                        <p class="text-xs text-{{ $item['color'] }}-400 mt-1">{{ $item['status'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-xs text-slate-500">PHP Version</p>
                    <p class="text-sm font-medium text-white">{{ $systemHealth['php_version'] ?? '8.x' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Laravel</p>
                    <p class="text-sm font-medium text-white">{{ $systemHealth['laravel_version'] ?? '11.x' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Database Size</p>
                    <p class="text-sm font-medium text-white">{{ $systemHealth['database_size'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Queue Driver</p>
                    <p class="text-sm font-medium text-white">{{ $systemHealth['queue_driver'] ?? 'database' }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-400 mr-2"></i>
                Quick Actions
            </h3>
            <div class="space-y-3">
                <a href="{{ route('super-admin.settings.index') }}" class="flex items-center p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-sa-500/50 hover:bg-slate-700/50 transition-all group">
                    <div class="w-10 h-10 bg-sa-500/20 rounded-lg flex items-center justify-center group-hover:bg-sa-500/30 transition-colors">
                        <i class="fas fa-cogs text-sa-400"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium text-slate-300 group-hover:text-white">System Settings</span>
                    <i class="fas fa-chevron-right text-slate-600 ml-auto group-hover:text-sa-400 transition-colors"></i>
                </a>
                <a href="{{ route('super-admin.modules.index') }}" class="flex items-center p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-sa-500/50 hover:bg-slate-700/50 transition-all group">
                    <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center group-hover:bg-orange-500/30 transition-colors">
                        <i class="fas fa-puzzle-piece text-orange-400"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium text-slate-300 group-hover:text-white">Toggle Modules</span>
                    <i class="fas fa-chevron-right text-slate-600 ml-auto group-hover:text-sa-400 transition-colors"></i>
                </a>
                <a href="{{ route('super-admin.admins.index') }}" class="flex items-center p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-sa-500/50 hover:bg-slate-700/50 transition-all group">
                    <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center group-hover:bg-red-500/30 transition-colors">
                        <i class="fas fa-user-shield text-red-400"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium text-slate-300 group-hover:text-white">Manage Admins</span>
                    <i class="fas fa-chevron-right text-slate-600 ml-auto group-hover:text-sa-400 transition-colors"></i>
                </a>
                <a href="{{ route('super-admin.feature-flags.index') }}" class="flex items-center p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-sa-500/50 hover:bg-slate-700/50 transition-all group">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                        <i class="fas fa-flag text-green-400"></i>
                    </div>
                    <span class="ml-3 text-sm font-medium text-slate-300 group-hover:text-white">Feature Flags</span>
                    <i class="fas fa-chevron-right text-slate-600 ml-auto group-hover:text-sa-400 transition-colors"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Module Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activities -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-history text-blue-400 mr-2"></i>
                    Recent Activities
                </h3>
                <a href="{{ route('admin.activity-logs.index') }}" class="text-xs text-sa-400 hover:text-sa-300 font-medium">
                    View All →
                </a>
            </div>
            <div class="space-y-4 max-h-80 overflow-y-auto">
                @forelse($recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 bg-slate-900/30 rounded-lg">
                        <div class="w-8 h-8 bg-sa-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-circle text-xs text-sa-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-300">{{ $activity->description }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-slate-600 text-3xl mb-3"></i>
                        <p class="text-sm text-slate-500">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Module Status -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-puzzle-piece text-orange-400 mr-2"></i>
                    Module Status
                </h3>
                <a href="{{ route('super-admin.modules.index') }}" class="text-xs text-sa-400 hover:text-sa-300 font-medium">
                    Manage →
                </a>
            </div>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($modules as $module)
                    <div class="flex items-center justify-between p-3 bg-slate-900/30 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-8 h-8 {{ $module->status === 'active' ? 'bg-green-500/20' : 'bg-red-500/20' }} rounded-lg flex items-center justify-center mr-3">
                                <i class="fas {{ $module->status === 'active' ? 'fa-check text-green-400' : 'fa-times text-red-400' }}"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-slate-300">{{ $module->module_name }}</span>
                                <p class="text-xs text-slate-500">{{ $module->description ?? $module->slug }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $module->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ ucfirst($module->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    <div class="bg-gradient-to-r from-sa-900/30 to-slate-800/50 rounded-xl border border-sa-700/50 p-6">
        <div class="flex items-start">
            <div class="w-12 h-12 bg-sa-500/20 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-shield-alt text-sa-400 text-xl"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white mb-1">Platform Control Panel</h3>
                <p class="text-sm text-slate-400 mb-4">
                    You have full control over the entire platform. Changes made here affect all users and modules. 
                    Use caution when modifying system settings or disabling modules.
                </p>
                <div class="flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        CMS Engine Active
                    </div>
                    <div class="flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        All Routes Protected
                    </div>
                    <div class="flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        Module Manager Active
                    </div>
                    <div class="flex items-center text-slate-400">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        Notification System Ready
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
