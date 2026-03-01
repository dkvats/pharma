<!-- Dashboard -->
<a href="{{ route('store.dashboard') }}" class="nav-link {{ request()->routeIs('store.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    <span>Dashboard</span>
</a>

<!-- Inventory -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventory</p>
    <a href="{{ route('store.stock.index') }}" class="nav-link {{ request()->routeIs('store.stock.*') ? 'active' : '' }}">
        <i data-lucide="package" class="w-5 h-5"></i>
        <span>Stock Management</span>
    </a>
    <a href="{{ route('store.reports.sales') }}" class="nav-link {{ request()->routeIs('store.reports.*') ? 'active' : '' }}">
        <i data-lucide="plus-circle" class="w-5 h-5"></i>
        <span>Record Sale</span>
    </a>
</div>

<!-- Orders -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</p>
    <a href="{{ route('store.orders.index') }}" class="nav-link {{ request()->routeIs('store.orders.*') ? 'active' : '' }}">
        <i data-lucide="list" class="w-5 h-5"></i>
        <span>My Orders</span>
    </a>
    <a href="{{ route('orders.create') }}" class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
        <i data-lucide="plus" class="w-5 h-5"></i>
        <span>Place Order</span>
    </a>
</div>

<!-- Referrals -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Referrals</p>
    <a href="{{ route('store.referrals') }}" class="nav-link {{ request()->routeIs('store.referrals') ? 'active' : '' }}">
        <i data-lucide="user-check" class="w-5 h-5"></i>
        <span>Doctor Referrals</span>
    </a>
</div>

<!-- Reports -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
    <a href="{{ route('store.reports.sales') }}" class="nav-link {{ request()->routeIs('store.reports.*') ? 'active' : '' }}">
        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
        <span>Sales Report</span>
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
