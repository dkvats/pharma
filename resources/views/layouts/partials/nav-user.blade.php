<!-- Dashboard -->
<a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    <span>Dashboard</span>
</a>

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

<!-- Orders -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</p>
    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
        <span>My Orders</span>
    </a>
</div>

<!-- Account -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
    <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i data-lucide="user-circle" class="w-5 h-5"></i>
        <span>Profile</span>
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
