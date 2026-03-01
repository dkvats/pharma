<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('doctor.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.dashboard') ? 'bg-slate-700 text-white' : '' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
    </a>

    <!-- Orders Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Orders</p>
        <a href="{{ route('orders.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('orders.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-shopping-bag w-5 text-center"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('products.catalog') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('products.catalog') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-pills w-5 text-center"></i>
            <span>Browse Products</span>
        </a>
        <a href="{{ route('cart.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('cart.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-shopping-cart w-5 text-center"></i>
            <span>My Cart</span>
        </a>
        <a href="{{ route('wishlist.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('wishlist.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-heart w-5 text-center"></i>
            <span>My Wishlist</span>
        </a>
    </div>

    <!-- Referral Sales -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Referral Sales</p>
        <a href="{{ route('doctor.referrals') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.referrals') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-friends w-5 text-center"></i>
            <span>View Referrals</span>
        </a>
    </div>

    <!-- Incentives Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Incentives</p>
        <a href="{{ route('doctor.targets.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.targets.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-bullseye w-5 text-center"></i>
            <span>Monthly Targets</span>
        </a>
        <a href="{{ route('doctor.spin.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.spin.*') && !request()->routeIs('doctor.spin.history') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-dharmachakra w-5 text-center"></i>
            <span>Spin & Win</span>
        </a>
        <a href="{{ route('doctor.spin.history') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.spin.history') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-history w-5 text-center"></i>
            <span>Reward History</span>
        </a>
    </div>

    <!-- Reports Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reports</p>
        <a href="{{ route('doctor.reports.performance') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('doctor.reports.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>My Performance</span>
        </a>
    </div>

    <!-- Profile -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Account</p>
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('profile.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-circle w-5 text-center"></i>
            <span>Profile</span>
        </a>
    </div>
</div>
