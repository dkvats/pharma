<!-- Super Admin Sidebar Navigation -->
<div class="px-3 space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.dashboard') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-tachometer-alt w-5 mr-3 {{ request()->routeIs('super-admin.dashboard') ? 'text-sa-200' : '' }}"></i>
        <span>Dashboard</span>
        @if(request()->routeIs('super-admin.dashboard'))
            <span class="ml-auto w-2 h-2 bg-sa-300 rounded-full"></span>
        @endif
    </a>
</div>

<!-- CMS Engine Section -->
<div class="mt-6 px-3">
    <p class="px-4 text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center">
        <i class="fas fa-database mr-2"></i>
        CMS Engine
    </p>
</div>
<div class="mt-2 px-3 space-y-1">
    <!-- System Settings -->
    <a href="{{ route('super-admin.settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.settings.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-cogs w-5 mr-3 {{ request()->routeIs('super-admin.settings.*') ? 'text-sa-200' : '' }}"></i>
        <span>System Settings</span>
        <span class="ml-auto text-xs text-green-400"><i class="fas fa-circle text-[6px]"></i></span>
    </a>

    <!-- Modules -->
    <a href="{{ route('super-admin.modules.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.modules.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-puzzle-piece w-5 mr-3 {{ request()->routeIs('super-admin.modules.*') ? 'text-sa-200' : '' }}"></i>
        <span>Modules</span>
        <span class="ml-auto px-1.5 py-0.5 text-xs rounded bg-sa-500/20 text-sa-400">10</span>
    </a>

    <!-- CMS Pages -->
    <a href="{{ route('super-admin.cms-pages.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.cms-pages.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-file-alt w-5 mr-3 {{ request()->routeIs('super-admin.cms-pages.*') ? 'text-sa-200' : '' }}"></i>
        <span>CMS Pages</span>
    </a>

    <!-- UI Settings -->
    <a href="{{ route('super-admin.ui-settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.ui-settings.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-palette w-5 mr-3 {{ request()->routeIs('super-admin.ui-settings.*') ? 'text-sa-200' : '' }}"></i>
        <span>UI Settings</span>
    </a>

    <!-- Dashboard Widgets -->
    <a href="{{ route('super-admin.widgets.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.widgets.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-th-large w-5 mr-3 {{ request()->routeIs('super-admin.widgets.*') ? 'text-sa-200' : '' }}"></i>
        <span>Dashboard Widgets</span>
    </a>

    <!-- Notification Templates -->
    <a href="{{ route('super-admin.notifications.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.notifications.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-bell w-5 mr-3 {{ request()->routeIs('super-admin.notifications.*') ? 'text-sa-200' : '' }}"></i>
        <span>Notifications</span>
        <span class="ml-auto px-1.5 py-0.5 text-xs rounded bg-green-500/20 text-green-400">7</span>
    </a>

    <!-- Feature Flags -->
    <a href="{{ route('super-admin.feature-flags.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.feature-flags.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-flag w-5 mr-3 {{ request()->routeIs('super-admin.feature-flags.*') ? 'text-sa-200' : '' }}"></i>
        <span>Feature Flags</span>
    </a>

    <!-- Homepage CMS -->
    <a href="{{ route('super-admin.homepage-cms.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.homepage-cms.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-globe w-5 mr-3 {{ request()->routeIs('super-admin.homepage-cms.*') ? 'text-sa-200' : '' }}"></i>
        <span>Homepage CMS</span>
        <span class="ml-auto px-1.5 py-0.5 text-xs rounded bg-green-500/20 text-green-400">New</span>
    </a>
</div>

<!-- Management Section -->
<div class="mt-6 px-3">
    <p class="px-4 text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center">
        <i class="fas fa-users-cog mr-2"></i>
        Management
    </p>
</div>
<div class="mt-2 px-3 space-y-1">
    <!-- Admin Management -->
    <a href="{{ route('super-admin.admins.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('super-admin.admins.*') ? 'bg-gradient-to-r from-sa-600 to-sa-700 text-white sa-nav-active' : 'text-slate-400 hover:bg-sidebar-hover hover:text-white' }}">
        <i class="fas fa-user-shield w-5 mr-3 {{ request()->routeIs('super-admin.admins.*') ? 'text-sa-200' : '' }}"></i>
        <span>Admin Users</span>
    </a>
</div>

<!-- Quick Links Section -->
<div class="mt-6 px-3">
    <p class="px-4 text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center">
        <i class="fas fa-link mr-2"></i>
        Quick Links
    </p>
</div>
<div class="mt-2 px-3 space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:bg-sidebar-hover hover:text-white">
        <i class="fas fa-external-link-alt w-5 mr-3"></i>
        <span>Admin Panel</span>
    </a>
    <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:bg-sidebar-hover hover:text-white">
        <i class="fas fa-history w-5 mr-3"></i>
        <span>Activity Logs</span>
    </a>
    <a href="{{ route('admin.reports.sales') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-slate-400 hover:bg-sidebar-hover hover:text-white">
        <i class="fas fa-chart-bar w-5 mr-3"></i>
        <span>Reports</span>
    </a>
</div>
