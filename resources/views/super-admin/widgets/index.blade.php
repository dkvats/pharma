@extends('layouts.super-admin')

@section('title', 'Dashboard Widgets')
@section('page-title', 'Dashboard Widgets')
@section('page-subtitle', 'Configure dashboard widget visibility')

@section('content')
<div class="space-y-6">
    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-lg"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Widget Management</h3>
                <p class="text-sm text-blue-700 mt-1">
                    Control which widgets appear on each role's dashboard. Sort order determines the position of widgets.
                </p>
            </div>
        </div>
    </div>

    <!-- Filter by Role -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Filter by Role:</label>
            <select id="roleFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Widgets List -->
    <form method="POST" action="{{ route('super-admin.widgets.update') }}">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">All Widgets</h3>
                    <p class="text-sm text-gray-500 mt-1">Enable/disable widgets and set their order</p>
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Widget</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sort Order</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Toggle</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($widgets as $widget)
                            <tr class="hover:bg-gray-50 widget-row" data-role="{{ $widget->role }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-th-large text-purple-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $widget->widget_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $widget->widget_key ?? 'custom' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $widget->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $widget->status === 'enabled' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($widget->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="number" name="widgets[{{ $widget->id }}][sort_order]" value="{{ $widget->sort_order }}" class="w-20 px-2 py-1 border border-gray-300 rounded text-center text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="widgets[{{ $widget->id }}][status]" value="enabled" class="sr-only peer" {{ $widget->status === 'enabled' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('roleFilter').addEventListener('change', function() {
        const selectedRole = this.value;
        const rows = document.querySelectorAll('.widget-row');
        rows.forEach(row => {
            if (!selectedRole || row.dataset.role === selectedRole) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush
@endsection
