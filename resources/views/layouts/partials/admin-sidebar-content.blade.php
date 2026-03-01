<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-slate-700 text-white' : '' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
    </a>

    <!-- User Management Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">User Management</p>
        <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span>All Users</span>
        </a>
    </div>

    <!-- Doctors Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Doctors</p>
        <a href="{{ route('admin.doctors.approval.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.doctors.approval.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-md w-5 text-center"></i>
            <span>Doctor Approval</span>
        </a>
    </div>

    <!-- Stores Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Stores</p>
        <a href="{{ route('admin.stores.approval.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.stores.approval.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-store w-5 text-center"></i>
            <span>Store Approval</span>
        </a>
        <a href="{{ route('admin.store-stock.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.store-stock.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-warehouse w-5 text-center"></i>
            <span>Store Stock</span>
        </a>
    </div>

    <!-- Orders Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Orders</p>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-shopping-cart w-5 text-center"></i>
            <span>All Orders</span>
        </a>
    </div>

    <!-- Products Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Products</p>
        <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-pills w-5 text-center"></i>
            <span>Manage Products</span>
        </a>
        <a href="{{ route('admin.offers.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.offers.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-tags w-5 text-center"></i>
            <span>Manage Offers</span>
        </a>
    </div>

    <!-- Territory Management -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Territory</p>
        <a href="{{ route('admin.territory.states') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.territory.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-map-marked-alt w-5 text-center"></i>
            <span>Territory Management</span>
        </a>
    </div>

    <!-- Reports Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reports</p>
        <a href="{{ route('admin.reports.dashboard') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.reports.dashboard') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-pie w-5 text-center"></i>
            <span>Analytics Dashboard</span>
        </a>
        <a href="{{ route('admin.reports.sales') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.reports.sales') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Sales Reports</span>
        </a>
        <a href="{{ route('admin.reports.doctors') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.reports.doctors') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-md w-5 text-center"></i>
            <span>Doctor Reports</span>
        </a>
        <a href="{{ route('admin.reports.stores') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.reports.stores') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-store w-5 text-center"></i>
            <span>Store Reports</span>
        </a>
        <a href="{{ route('admin.reports.sales-by-entity') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.reports.sales-by-entity') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-bar w-5 text-center"></i>
            <span>Sales by Entity</span>
        </a>
    </div>

    <!-- Rewards Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Rewards</p>
        <a href="{{ route('admin.rewards.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.rewards.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-gift w-5 text-center"></i>
            <span>Manage Rewards</span>
        </a>
        <a href="{{ route('admin.spin-control.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.spin-control.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-bullseye w-5 text-center"></i>
            <span>Spin Control</span>
        </a>
        <a href="{{ route('admin.spin-campaigns.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.spin-campaigns.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-globe w-5 text-center"></i>
            <span>Spin Campaigns</span>
        </a>
        <a href="{{ route('admin.grand-spin-rewards.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.grand-spin-rewards.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-trophy w-5 text-center"></i>
            <span>Grand Spin Rewards</span>
        </a>
        <a href="{{ route('admin.grand-draw.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.grand-draw.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-dice w-5 text-center"></i>
            <span>Grand Lucky Draw</span>
        </a>
    </div>

    <!-- Leaderboard -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Leaderboard</p>
        <a href="{{ route('leaderboard.monthly') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('leaderboard.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-medal w-5 text-center"></i>
            <span>Leaderboards</span>
        </a>
    </div>

    <!-- Audit Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Audit</p>
        <a href="{{ route('admin.referrals.audit') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.referrals.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-search w-5 text-center"></i>
            <span>Referral Audit</span>
        </a>
        <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.activity-logs.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-clipboard-list w-5 text-center"></i>
            <span>Activity Logs</span>
        </a>
    </div>

    <!-- Settings -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Settings</p>
        <a href="{{ route('admin.settings.spin') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-cog w-5 text-center"></i>
            <span>Spin Settings</span>
        </a>
    </div>
</div>
