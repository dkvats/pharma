<!-- Dashboard -->
<a href="{{ route('mr.dashboard') }}" class="nav-link {{ request()->routeIs('mr.dashboard') ? 'active' : '' }}">
    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
    <span>Dashboard</span>
</a>

<!-- My Doctors -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">My Doctors</p>
    <a href="{{ route('mr.doctors.index') }}" class="nav-link {{ request()->routeIs('mr.doctors.index') ? 'active' : '' }}">
        <i data-lucide="list" class="w-5 h-5"></i>
        <span>Doctor List</span>
    </a>
    <a href="{{ route('mr.doctors.create') }}" class="nav-link {{ request()->routeIs('mr.doctors.create') ? 'active' : '' }}">
        <i data-lucide="user-plus" class="w-5 h-5"></i>
        <span>Register Doctor</span>
    </a>
</div>

<!-- Daily Activities -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Activities</p>
    <a href="{{ route('mr.visits.index') }}" class="nav-link {{ request()->routeIs('mr.visits.*') ? 'active' : '' }}">
        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
        <span>Visits (DCR)</span>
    </a>
    <a href="{{ route('mr.samples.index') }}" class="nav-link {{ request()->routeIs('mr.samples.*') ? 'active' : '' }}">
        <i data-lucide="flask-conical" class="w-5 h-5"></i>
        <span>Samples</span>
    </a>
</div>

<!-- Reports -->
<div class="pt-4">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</p>
    <a href="{{ route('mr.reports.daily') }}" class="nav-link {{ request()->routeIs('mr.reports.daily') ? 'active' : '' }}">
        <i data-lucide="calendar" class="w-5 h-5"></i>
        <span>Daily Report</span>
    </a>
    <a href="{{ route('mr.reports.weekly') }}" class="nav-link {{ request()->routeIs('mr.reports.weekly') ? 'active' : '' }}">
        <i data-lucide="calendar-days" class="w-5 h-5"></i>
        <span>Weekly Report</span>
    </a>
    <a href="{{ route('mr.reports.monthly') }}" class="nav-link {{ request()->routeIs('mr.reports.monthly') ? 'active' : '' }}">
        <i data-lucide="calendar-range" class="w-5 h-5"></i>
        <span>Monthly Report</span>
    </a>
    <a href="{{ route('mr.reports.performance') }}" class="nav-link {{ request()->routeIs('mr.reports.performance') ? 'active' : '' }}">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        <span>Performance</span>
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
