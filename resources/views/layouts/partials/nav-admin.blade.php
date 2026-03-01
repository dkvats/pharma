<!-- Dashboard -->
<a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    <span>Dashboard</span>
</a>

<!-- Users Management -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Users</p>
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span>All Users</span>
    </a>
</div>

<!-- Doctors -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Doctors</p>
    <a href="{{ route('admin.doctors.approval.index') }}" class="nav-link {{ request()->routeIs('admin.doctors.approval.*') ? 'active' : '' }}">
        <i data-lucide="user-check" class="w-5 h-5"></i>
        <span>Doctor Approval</span>
        @php
            $pendingCount = \App\Models\MR\Doctor::pending()->count();
        @endphp
        @if($pendingCount > 0)
            <span class="ml-auto bg-danger-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
        @endif
    </a>
</div>

<!-- Products & Orders -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Operations</p>
    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i data-lucide="package" class="w-5 h-5"></i>
        <span>Products</span>
    </a>
    <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
        <span>Orders</span>
    </a>
    <a href="{{ route('admin.store-stock.index') }}" class="nav-link {{ request()->routeIs('admin.store-stock.*') ? 'active' : '' }}">
        <i data-lucide="store" class="w-5 h-5"></i>
        <span>Stores</span>
    </a>
</div>

<!-- Territory -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Territory</p>
    <a href="{{ route('admin.territory.states') }}" class="nav-link {{ request()->routeIs('admin.territory.*') ? 'active' : '' }}">
        <i data-lucide="map-pin" class="w-5 h-5"></i>
        <span>Territory Management</span>
    </a>
</div>

<!-- Reports -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Analytics</p>
    <a href="{{ route('admin.reports.dashboard') }}" class="nav-link {{ request()->routeIs('admin.reports.dashboard') ? 'active' : '' }}">
        <i data-lucide="pie-chart" class="w-5 h-5"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.reports.sales') }}" class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span>Sales Reports</span>
    </a>
    <a href="{{ route('admin.reports.sales-by-entity') }}" class="nav-link {{ request()->routeIs('admin.reports.sales-by-entity') ? 'active' : '' }}">
        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
        <span>Sales by Entity</span>
    </a>
    <a href="{{ route('leaderboard.monthly') }}" class="nav-link {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">
        <i data-lucide="trophy" class="w-5 h-5"></i>
        <span>Leaderboard</span>
    </a>
</div>

<!-- Rewards -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Rewards</p>
    <a href="{{ route('admin.rewards.index') }}" class="nav-link {{ request()->routeIs('admin.rewards.*') ? 'active' : '' }}">
        <i data-lucide="gift" class="w-5 h-5"></i>
        <span>Manage Rewards</span>
    </a>
    <a href="{{ route('admin.spin-control.index') }}" class="nav-link {{ request()->routeIs('admin.spin-control.*') ? 'active' : '' }}">
        <i data-lucide="target" class="w-5 h-5"></i>
        <span>Spin Control</span>
    </a>
    <a href="{{ route('admin.grand-draw.index') }}" class="nav-link {{ request()->routeIs('admin.grand-draw.*') ? 'active' : '' }}">
        <i data-lucide="dice-5" class="w-5 h-5"></i>
        <span>Grand Draw</span>
    </a>
</div>

<!-- Audit -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Audit</p>
    <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
        <span>Activity Logs</span>
    </a>
</div>

<style>
.nav-link {
    @apply flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors;
}
.nav-link.active {
    @apply bg-gray-700 text-white;
}
</style>
