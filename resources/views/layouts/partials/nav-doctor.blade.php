<!-- Dashboard -->
<a href="{{ route('doctor.dashboard') }}" class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    <span>Dashboard</span>
</a>

<!-- Orders -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</p>
    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
        <span>My Orders</span>
    </a>
    <a href="{{ route('doctor.referrals') }}" class="nav-link {{ request()->routeIs('doctor.referrals') ? 'active' : '' }}">
        <i data-lucide="users" class="w-5 h-5"></i>
        <span>Referral Sales</span>
    </a>
</div>

<!-- Shop -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Shop</p>
    <a href="{{ route('products.catalog') }}" class="nav-link {{ request()->routeIs('products.catalog') ? 'active' : '' }}">
        <i data-lucide="package" class="w-5 h-5"></i>
        <span>Browse Products</span>
    </a>
    <a href="{{ route('cart.index') }}" class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
        <span>My Cart</span>
    </a>
    <a href="{{ route('wishlist.index') }}" class="nav-link {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
        <i data-lucide="heart" class="w-5 h-5"></i>
        <span>Wishlist</span>
    </a>
</div>

<!-- Incentives -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Incentives</p>
    <a href="{{ route('doctor.targets.index') }}" class="nav-link {{ request()->routeIs('doctor.targets.*') ? 'active' : '' }}">
        <i data-lucide="target" class="w-5 h-5"></i>
        <span>Monthly Targets</span>
    </a>
    <a href="{{ route('doctor.spin.index') }}" class="nav-link {{ request()->routeIs('doctor.spin.*') ? 'active' : '' }}">
        <i data-lucide="gift" class="w-5 h-5"></i>
        <span>Spin & Rewards</span>
    </a>
</div>

<!-- Reports -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
    <a href="{{ route('doctor.reports.performance') }}" class="nav-link {{ request()->routeIs('doctor.reports.*') ? 'active' : '' }}">
        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
        <span>My Performance</span>
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
