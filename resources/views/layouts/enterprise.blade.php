<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Pharma ERP - Enterprise Pharmaceutical Management System">

    <title>@yield('title', 'Dashboard') | Pharma ERP</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Tailwind CSS (fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        },
                        success: {
                            50: '#f0fdf4',
                            500: '#22c55e',
                            600: '#16a34a',
                        },
                        warning: {
                            50: '#fffbeb',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        danger: {
                            50: '#fef2f2',
                            500: '#ef4444',
                            600: '#dc2626',
                        }
                    },
                    boxShadow: {
                        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                        'card': '0 0 0 1px rgba(0, 0, 0, 0.05), 0 1px 3px 0 rgba(0, 0, 0, 0.1)',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        html, body { height: 100%; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        
        @auth
            <!-- Sidebar -->
            <aside id="sidebar" class="w-64 bg-gray-800 text-white flex-shrink-0 flex flex-col fixed h-screen z-30 transform transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0">
                <!-- Logo -->
                <div class="flex items-center h-16 px-6 border-b border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                            <i data-lucide="pill" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-white leading-tight">Pharma ERP</h1>
                            <p class="text-xs text-gray-400">Enterprise Edition</p>
                        </div>
                    </div>
                </div>

                <!-- User Info Card -->
                <div class="p-4 mx-4 mt-4 bg-gray-700 rounded-xl border border-gray-600">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-6 h-6 text-gray-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if(auth()->user()->hasRole('Admin')) bg-red-900 text-red-300
                                @elseif(auth()->user()->hasRole('Doctor')) bg-blue-900 text-blue-300
                                @elseif(auth()->user()->hasRole('Store')) bg-amber-900 text-amber-300
                                @elseif(auth()->user()->hasRole('MR')) bg-purple-900 text-purple-300
                                @else bg-gray-600 text-gray-300
                                @endif">
                                {{ auth()->user()->roles->first()->name ?? 'User' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    @if(auth()->user()->hasRole('Admin'))
                        @include('layouts.partials.nav-admin')
                    @elseif(auth()->user()->hasRole('MR'))
                        @include('layouts.partials.nav-mr')
                    @elseif(auth()->user()->hasRole('Doctor'))
                        @include('layouts.partials.nav-doctor')
                    @elseif(auth()->user()->hasRole('Store'))
                        @include('layouts.partials.nav-store')
                    @else
                        @include('layouts.partials.nav-user')
                    @endif
                </nav>

                <!-- Bottom Actions -->
                <div class="p-4 border-t border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full gap-3 px-4 py-3 text-sm font-medium text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </aside>
        @endauth

        <!-- Main Content -->
        <div class="flex-1 ml-0 lg:ml-64 flex flex-col min-h-screen">
            
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-40">
                <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Left Section -->
                    <div class="flex items-center gap-4 flex-shrink-0">
                        @auth
                            <!-- Mobile menu button -->
                            <button id="mobile-menu-btn" type="button" class="lg:hidden p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-600">
                                <i data-lucide="menu" class="w-6 h-6"></i>
                            </button>
                        @endauth
                        
                        <!-- Breadcrumbs -->
                        <nav class="hidden sm:flex flex-shrink-0" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-sm">
                                <li>
                                    <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">
                                        <i data-lucide="home" class="w-4 h-4"></i>
                                    </a>
                                </li>
                                @yield('breadcrumbs')
                            </ol>
                        </nav>
                    </div>

                    <!-- Spacer -->
                    <div class="flex-1"></div>

                    <!-- Right Section -->
                    <div class="flex items-center gap-4 flex-shrink-0">
                        @auth
                            @php
                                $user = auth()->user();
                                $canUseCart = $user->hasRole('Doctor') || $user->hasRole('End User');
                            @endphp

                            @if($canUseCart)
                                <!-- Cart -->
                                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-600 flex-shrink-0">
                                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                    @php $cartCount = $user->cart?->items()->sum('quantity') ?? 0; @endphp
                                    @if($cartCount > 0)
                                        <span class="absolute top-1 right-1 w-4 h-4 bg-danger-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                            {{ $cartCount }}
                                        </span>
                                    @endif
                                </a>

                                <!-- Wishlist -->
                                <a href="{{ route('wishlist.index') }}" class="relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-600 flex-shrink-0">
                                    <i data-lucide="heart" class="w-5 h-5"></i>
                                    @php $wishlistCount = $user->wishlist?->items()->count() ?? 0; @endphp
                                    @if($wishlistCount > 0)
                                        <span class="absolute top-1 right-1 w-4 h-4 bg-danger-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                            {{ $wishlistCount }}
                                        </span>
                                    @endif
                                </a>
                            @endif

                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-500 rounded-lg hover:bg-gray-100 hover:text-gray-600 flex-shrink-0">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-primary-500 rounded-full"></span>
                            </button>

                            <!-- Profile Dropdown -->
                            <div class="relative flex-shrink-0" x-data="{ open: false }" x-cloak>
                                <button @click="open = !open" type="button"
                                        class="w-9 h-9 rounded-full bg-indigo-500 text-white flex items-center justify-center font-semibold text-sm hover:bg-indigo-600 transition-colors cursor-pointer">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </button>
                                
                                <!-- Profile Dropdown Menu -->
                                <div x-show="open" 
                                     @click.outside="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50 origin-top-right">
                                    <div class="py-1">
                                        <a href="{{ route('profile.edit') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                           Profile
                                        </a>
                                        <a href="{{ route('profile.password') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                           Change Password
                                        </a>
                                        <div class="border-t border-gray-200 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-gray-100">
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900 flex-shrink-0">Sign In</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 flex-shrink-0">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-xl bg-success-50 border border-success-200 p-4 animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-success-600"></i>
                            <p class="text-sm font-medium text-success-800">{{ session('success') }}</p>
                            <button onclick="this.closest('.animate-fade-in').remove()" class="ml-auto text-success-600 hover:text-success-800">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-xl bg-danger-50 border border-danger-200 p-4 animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-danger-600"></i>
                            <p class="text-sm font-medium text-danger-800">{{ session('error') }}</p>
                            <button onclick="this.closest('.animate-fade-in').remove()" class="ml-auto text-danger-600 hover:text-danger-800">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 rounded-xl bg-warning-50 border border-warning-200 p-4 animate-fade-in">
                        <div class="flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-warning-600"></i>
                            <p class="text-sm font-medium text-warning-800">{{ session('warning') }}</p>
                            <button onclick="this.closest('.animate-fade-in').remove()" class="ml-auto text-warning-600 hover:text-warning-800">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Page Title -->
                @hasSection('page-title')
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('page-title')</h1>
                        @hasSection('page-description')
                            <p class="mt-1 text-sm text-gray-600">@yield('page-description')</p>
                        @endif
                    </div>
                @endif

                <!-- Content -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Pharma ERP. All rights reserved.</p>
                    <p class="mt-2 sm:mt-0">Version 2.0 Enterprise</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay - Only visible when sidebar is open -->
    <div id="sidebar-overlay" class="fixed inset-0 z-20 bg-gray-900/50 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Scripts -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.getElementById('mobile-menu-btn')?.addEventListener('click', toggleSidebar);

        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.5s ease';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        
        /* AlpineJS cloak - hide until initialized */
        [x-cloak] { display: none !important; }
    </style>

    @stack('scripts')
</body>
</html>
