<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MR Dashboard') - Pharma ERP</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="bg-indigo-800 text-white w-64 flex-shrink-0 hidden md:block">
            <div class="p-4 border-b border-indigo-700">
                <h1 class="text-xl font-bold">MR Portal</h1>
                <p class="text-sm text-indigo-300">{{ auth()->user()->name }}</p>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('mr.dashboard') }}" class="block py-3 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.dashboard') ? 'bg-indigo-900' : '' }}">
                    <i class="fas fa-tachometer-alt w-6"></i> Dashboard
                </a>
                
                <a href="{{ route('mr.doctors.index') }}" class="block py-3 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.doctors.*') ? 'bg-indigo-900' : '' }}">
                    <i class="fas fa-user-md w-6"></i> Doctors
                </a>
                
                <a href="{{ route('mr.visits.index') }}" class="block py-3 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.visits.*') ? 'bg-indigo-900' : '' }}">
                    <i class="fas fa-clipboard-list w-6"></i> Visits (DCR)
                </a>
                
                <a href="{{ route('mr.orders.index') }}" class="block py-3 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.orders.*') ? 'bg-indigo-900' : '' }}">
                    <i class="fas fa-shopping-cart w-6"></i> Orders
                </a>
                
                <a href="{{ route('mr.samples.index') }}" class="block py-3 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.samples.*') ? 'bg-indigo-900' : '' }}">
                    <i class="fas fa-flask w-6"></i> Samples
                </a>
                
                <div class="border-t border-indigo-700 mt-4 pt-4">
                    <p class="px-4 text-xs text-indigo-400 uppercase">Reports</p>
                    
                    <a href="{{ route('mr.reports.daily') }}" class="block py-2 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.reports.daily') ? 'bg-indigo-900' : '' }}">
                        <i class="fas fa-calendar-day w-6"></i> Daily Report
                    </a>
                    
                    <a href="{{ route('mr.reports.weekly') }}" class="block py-2 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.reports.weekly') ? 'bg-indigo-900' : '' }}">
                        <i class="fas fa-calendar-week w-6"></i> Weekly Report
                    </a>
                    
                    <a href="{{ route('mr.reports.monthly') }}" class="block py-2 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.reports.monthly') ? 'bg-indigo-900' : '' }}">
                        <i class="fas fa-calendar-alt w-6"></i> Monthly Report
                    </a>
                    
                    <a href="{{ route('mr.reports.doctors') }}" class="block py-2 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.reports.doctors') ? 'bg-indigo-900' : '' }}">
                        <i class="fas fa-users w-6"></i> Doctor Report
                    </a>
                    
                    <a href="{{ route('mr.reports.performance') }}" class="block py-2 px-4 hover:bg-indigo-700 {{ request()->routeIs('mr.reports.performance') ? 'bg-indigo-900' : '' }}">
                        <i class="fas fa-chart-line w-6"></i> Performance
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Mobile Header -->
        <div class="md:hidden fixed top-0 left-0 right-0 bg-indigo-800 text-white z-50">
            <div class="flex items-center justify-between p-4">
                <h1 class="text-lg font-bold">MR Portal</h1>
                <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            <nav id="mobile-menu" class="hidden bg-indigo-900">
                <a href="{{ route('mr.dashboard') }}" class="block py-2 px-4 hover:bg-indigo-700">Dashboard</a>
                <a href="{{ route('mr.doctors.index') }}" class="block py-2 px-4 hover:bg-indigo-700">Doctors</a>
                <a href="{{ route('mr.visits.index') }}" class="block py-2 px-4 hover:bg-indigo-700">Visits</a>
                <a href="{{ route('mr.orders.index') }}" class="block py-2 px-4 hover:bg-indigo-700">Orders</a>
                <a href="{{ route('mr.samples.index') }}" class="block py-2 px-4 hover:bg-indigo-700">Samples</a>
                <a href="{{ route('mr.reports.daily') }}" class="block py-2 px-4 hover:bg-indigo-700">Reports</a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <main class="flex-1 md:ml-0 mt-16 md:mt-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-home"></i> Main Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
