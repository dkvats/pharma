<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-700 text-white' : '' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
    </a>

    <!-- Products Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Products</p>
        <a href="{{ route('products.catalog') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('products.catalog') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-pills w-5 text-center"></i>
            <span>Browse Products</span>
        </a>
    </div>

    <!-- Shopping Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Shopping</p>
        <a href="{{ route('cart.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('cart.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-shopping-cart w-5 text-center"></i>
            <span>My Cart</span>
        </a>
        <a href="{{ route('wishlist.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('wishlist.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-heart w-5 text-center"></i>
            <span>My Wishlist</span>
        </a>
    </div>

    <!-- Orders Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Orders</p>
        <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('orders.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-box w-5 text-center"></i>
            <span>My Orders</span>
        </a>
    </div>

    <!-- Offers Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Offers</p>
        <a href="{{ route('offers.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('offers.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-tags w-5 text-center"></i>
            <span>My Offers</span>
        </a>
    </div>

    <!-- Account Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Account</p>
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('profile.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-circle w-5 text-center"></i>
            <span>Profile</span>
        </a>
    </div>
</div>
