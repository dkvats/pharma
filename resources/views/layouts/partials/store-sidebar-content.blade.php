<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('store.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.dashboard') ? 'bg-slate-700 text-white' : '' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
    </a>

    <!-- Stock Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Stock</p>
        <a href="{{ route('store.stock.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.stock.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-boxes w-5 text-center"></i>
            <span>Manage Stock</span>
        </a>
        <a href="{{ route('store.stock-register') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.stock-register') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-book w-5 text-center"></i>
            <span>Stock Register</span>
        </a>
    </div>

    <!-- Sales Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Sales</p>
        <a href="{{ route('store.stock.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.sales.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span>Record Sale</span>
        </a>
        <a href="{{ route('store.bulk-sale.form') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.bulk-sale.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-layer-group w-5 text-center"></i>
            <span>Bulk Sale Entry</span>
        </a>
    </div>

    <!-- Orders Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Orders</p>
        <a href="{{ route('store.orders.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.orders.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-list w-5 text-center"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('orders.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('orders.create') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-plus w-5 text-center"></i>
            <span>Place Order</span>
        </a>
    </div>

    <!-- Offers Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Offers</p>
        <a href="{{ route('store.offers.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.offers.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-tags w-5 text-center"></i>
            <span>Available Offers</span>
        </a>
    </div>

    <!-- Referral Entries -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Referrals</p>
        <a href="{{ route('store.referrals') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.referrals') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-md w-5 text-center"></i>
            <span>Referral Entries</span>
        </a>
    </div>

    <!-- Reports Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reports</p>
        <a href="{{ route('store.reports.sales') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('store.reports.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span>Sales Report</span>
        </a>
    </div>
</div>
