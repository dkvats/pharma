<div class="space-y-1">
    <!-- Dashboard -->
    <a href="{{ route('mr.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.dashboard') ? 'bg-slate-700 text-white' : '' }}">
        <i class="fas fa-tachometer-alt w-5 text-center"></i>
        <span>Dashboard</span>
    </a>

    <!-- Stores Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">My Stores</p>
        <a href="{{ route('mr.stores.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.stores.index') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-store w-5 text-center"></i>
            <span>Store List</span>
        </a>
        <a href="{{ route('mr.stores.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.stores.create') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-plus w-5 text-center"></i>
            <span>Register Store</span>
        </a>
    </div>

    <!-- Doctors Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">My Doctors</p>
        <a href="{{ route('mr.doctors.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.doctors.index') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-list w-5 text-center"></i>
            <span>Doctor List</span>
        </a>
        <a href="{{ route('mr.doctors.create') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.doctors.create') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-plus w-5 text-center"></i>
            <span>Register Doctor</span>
        </a>
    </div>

    <!-- Visits Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Daily Activities</p>
        <a href="{{ route('mr.visits.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.visits.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-clipboard-check w-5 text-center"></i>
            <span>Visits (DCR)</span>
        </a>
        <a href="{{ route('mr.samples.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.samples.*') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-flask w-5 text-center"></i>
            <span>Samples</span>
        </a>
    </div>

    <!-- Reports Section -->
    <div class="pt-4">
        <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Reports</p>
        <a href="{{ route('mr.reports.daily') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.reports.daily') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-calendar-day w-5 text-center"></i>
            <span>Daily Report</span>
        </a>
        <a href="{{ route('mr.reports.weekly') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.reports.weekly') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-calendar-week w-5 text-center"></i>
            <span>Weekly Report</span>
        </a>
        <a href="{{ route('mr.reports.monthly') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.reports.monthly') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-calendar-alt w-5 text-center"></i>
            <span>Monthly Report</span>
        </a>
        <a href="{{ route('mr.reports.doctors') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.reports.doctors') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-user-md w-5 text-center"></i>
            <span>Doctor Report</span>
        </a>
        <a href="{{ route('mr.reports.performance') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->routeIs('mr.reports.performance') ? 'bg-slate-700 text-white' : '' }}">
            <i class="fas fa-chart-line w-5 text-center"></i>
            <span>Performance</span>
        </a>
    </div>
</div>
