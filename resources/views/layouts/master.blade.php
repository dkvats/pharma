<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pharma Management System')</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        
        @auth
            <!-- Sidebar -->
            <aside class="bg-slate-800 text-white w-64 flex-shrink-0 hidden md:flex flex-col fixed h-full z-30">
                <!-- Logo Area -->
                <div class="p-4 border-b border-slate-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pills text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold">Pharma ERP</h1>
                            <p class="text-xs text-slate-400">Management System</p>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="p-4 border-b border-slate-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-slate-600 flex items-center justify-center">
                            <i class="fas fa-user text-slate-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if(auth()->user()->hasRole('Super Admin')) bg-purple-100 text-purple-800
                                @elseif(auth()->user()->hasRole('Admin')) bg-red-100 text-red-800
                                @elseif(auth()->user()->hasRole('Sub Admin')) bg-orange-100 text-orange-800
                                @elseif(auth()->user()->hasRole('Doctor')) bg-blue-100 text-blue-800
                                @elseif(auth()->user()->hasRole('Store')) bg-yellow-100 text-yellow-800
                                @elseif(auth()->user()->hasRole('MR')) bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ auth()->user()->roles->first()->name ?? 'User' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4">
                    @if(auth()->user()->hasRole('Super Admin'))
                        @include('layouts.partials.super-admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('Admin'))
                        @include('layouts.partials.admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('Sub Admin'))
                        @include('layouts.partials.admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('MR'))
                        @include('layouts.partials.mr-sidebar-content')
                    @elseif(auth()->user()->hasRole('Doctor'))
                        @include('layouts.partials.doctor-sidebar-content')
                    @elseif(auth()->user()->hasRole('Store'))
                        @include('layouts.partials.store-sidebar-content')
                    @else
                        @include('layouts.partials.user-sidebar-content')
                    @endif
                </nav>

                <!-- Logout -->
                <div class="p-4 border-t border-slate-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Mobile Sidebar Overlay -->
            <div id="mobileSidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" onclick="toggleMobileSidebar()"></div>

            <!-- Mobile Sidebar -->
            <aside id="mobileSidebar" class="hidden fixed left-0 top-0 h-full w-64 bg-slate-800 text-white z-50 md:hidden overflow-y-auto">
                <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pills text-white"></i>
                        </div>
                        <span class="font-bold">Pharma ERP</span>
                    </div>
                    <button onclick="toggleMobileSidebar()" class="text-slate-400 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="p-4 border-b border-slate-700">
                    <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                    <span class="text-xs text-slate-400">{{ auth()->user()->roles->first()->name ?? 'User' }}</span>
                </div>

                <nav class="py-4">
                    @if(auth()->user()->hasRole('Super Admin'))
                        @include('layouts.partials.super-admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('Admin'))
                        @include('layouts.partials.admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('Sub Admin'))
                        @include('layouts.partials.admin-sidebar-content')
                    @elseif(auth()->user()->hasRole('MR'))
                        @include('layouts.partials.mr-sidebar-content')
                    @elseif(auth()->user()->hasRole('Doctor'))
                        @include('layouts.partials.doctor-sidebar-content')
                    @elseif(auth()->user()->hasRole('Store'))
                        @include('layouts.partials.store-sidebar-content')
                    @else
                        @include('layouts.partials.user-sidebar-content')
                    @endif
                </nav>

                <div class="p-4 border-t border-slate-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </aside>
        @endauth

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col @auth md:ml-64 @endauth">
            
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            @auth
                                <!-- Mobile Menu Button -->
                                <button onclick="toggleMobileSidebar()" class="md:hidden text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-bars text-xl"></i>
                                </button>
                            @endauth
                            
                            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        </div>

                        <div class="flex items-center space-x-4">
                            @auth
                                @php
                                    $user = auth()->user();
                                    $canUseCart = $user->hasRole('Doctor') || $user->hasRole('End User');
                                @endphp

                                @if($canUseCart)
                                    <!-- Cart Icon -->
                                    <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-gray-900 p-2">
                                        <i class="fas fa-shopping-cart text-lg"></i>
                                        @php
                                            $cartCount = $user->cart?->items()->sum('quantity') ?? 0;
                                        @endphp
                                        @if($cartCount > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                                        @endif
                                    </a>

                                    <!-- Wishlist Icon -->
                                    <a href="{{ route('wishlist.index') }}" class="relative text-gray-600 hover:text-gray-900 p-2">
                                        <i class="fas fa-heart text-lg"></i>
                                        @php
                                            $wishlistCount = $user->wishlist?->items()->count() ?? 0;
                                        @endphp
                                        @if($wishlistCount > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $wishlistCount }}</span>
                                        @endif
                                    </a>
                                @endif

                                <!-- Notifications -->
                                <button class="relative text-gray-600 hover:text-gray-900 p-2">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 font-medium">Login</a>
                                <a href="{{ route('register') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 font-medium">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-yellow-400 hover:text-yellow-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-blue-400 hover:text-blue-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} Pharma ERP. All rights reserved.</p>
                    <p class="mt-2 md:mt-0">Version 1.0 | <span class="text-primary-600">Enterprise Edition</span></p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('mobileSidebarOverlay');
            
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('hidden');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessages = document.querySelectorAll('[class*="rounded-lg bg-"][class*="p-4"]');
            flashMessages.forEach(function(message) {
                setTimeout(function() {
                    message.style.transition = 'opacity 0.5s ease';
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
