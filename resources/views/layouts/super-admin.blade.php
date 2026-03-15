<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Super Admin - Platform Control Panel')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        // Super Admin specific color scheme - deep purple/indigo
                        sa: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#2e1065',
                        },
                        // Dark sidebar colors
                        'sidebar': {
                            'bg': '#0f0a1f',
                            'hover': '#1a1333',
                            'active': '#2d1f54',
                            'border': '#2d1f54',
                        }
                    }
                },
            },
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Custom scrollbar for Super Admin */
        .sa-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sa-sidebar::-webkit-scrollbar-track {
            background: #0f0a1f;
        }
        .sa-sidebar::-webkit-scrollbar-thumb {
            background: #4c1d95;
            border-radius: 2px;
        }
        .sa-sidebar::-webkit-scrollbar-thumb:hover {
            background: #6d28d9;
        }

        /* Glow effect for active items */
        .sa-nav-active {
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);
        }

        /* Pulse animation for status indicators */
        @keyframes sa-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .sa-pulse {
            animation: sa-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-900 min-h-screen">
    <div class="min-h-screen flex">
        
        <!-- Super Admin Sidebar -->
        <aside class="sa-sidebar bg-sidebar-bg text-white w-72 flex-shrink-0 hidden lg:flex flex-col fixed h-full z-30 border-r border-sidebar-border">
            <!-- Logo Area - Platform Control Panel -->
            <div class="p-5 border-b border-sidebar-border">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-sa-500 to-sa-700 rounded-xl flex items-center justify-center shadow-lg shadow-sa-500/20">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-sa-300 to-sa-500 bg-clip-text text-transparent">Platform Control</h1>
                        <p class="text-xs text-slate-500 font-medium tracking-wider">SUPER ADMIN</p>
                    </div>
                </div>
            </div>

            <!-- Super Admin Badge -->
            <div class="px-5 py-3 border-b border-sidebar-border">
                <div class="flex items-center space-x-3 bg-gradient-to-r from-sa-900/50 to-sa-800/30 rounded-lg p-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-sa-400 to-sa-600 flex items-center justify-center">
                        <i class="fas fa-crown text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-sa-500/20 text-sa-300 border border-sa-500/30">
                            <i class="fas fa-check-circle mr-1"></i>
                            FULL ACCESS
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                @include('layouts.partials.super-admin-sidebar-content')
            </nav>

            <!-- System Status -->
            <div class="p-4 border-t border-sidebar-border">
                <div class="bg-sidebar-hover rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-slate-400">System Status</span>
                        <span class="flex items-center text-xs text-green-400">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1 sa-pulse"></span>
                            Online
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="text-slate-400">Version</div>
                        <div class="text-white text-right">2.0.0</div>
                        <div class="text-slate-400">Environment</div>
                        <div class="text-white text-right">{{ app()->environment() }}</div>
                    </div>
                </div>
            </div>

            <!-- Logout -->
            <div class="p-4 border-t border-sidebar-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-slate-400 hover:bg-red-900/30 hover:text-red-400 transition-all duration-200 group">
                        <i class="fas fa-sign-out-alt group-hover:scale-110 transition-transform"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div id="mobileSidebarOverlay" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-40 lg:hidden" onclick="toggleMobileSidebar()"></div>

        <!-- Mobile Sidebar -->
        <aside id="mobileSidebar" class="hidden fixed left-0 top-0 h-full w-72 bg-sidebar-bg text-white z-50 lg:hidden overflow-y-auto">
            <div class="p-4 border-b border-sidebar-border flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-sa-500 to-sa-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <span class="font-bold text-white">Platform Control</span>
                        <p class="text-xs text-slate-500">Super Admin</p>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-4 border-b border-sidebar-border">
                <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                <span class="text-xs text-sa-400">Super Admin</span>
            </div>

            <nav class="py-4">
                @include('layouts.partials.super-admin-sidebar-content')
            </nav>

            <div class="p-4 border-t border-sidebar-border">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2 rounded-lg text-slate-300 hover:bg-red-900/30 hover:text-red-400">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col lg:ml-72">
            
            <!-- Top Navigation Bar - Dark Theme -->
            <header class="bg-slate-800 border-b border-slate-700 sticky top-0 z-20 shadow-lg">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Mobile Menu Button -->
                            <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            
                            <div>
                                <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>
                                <p class="text-xs text-slate-500">@yield('page-subtitle', 'Platform Management')</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Quick Actions -->
                            <div class="hidden md:flex items-center space-x-2">
                                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-700 text-slate-300 hover:bg-slate-600 hover:text-white transition-colors">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Admin Panel
                                </a>
                            </div>

                            <!-- Notifications -->
                            <button class="relative text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700 transition-colors">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-sa-500 rounded-full"></span>
                            </button>

                            <!-- Settings Quick Link -->
                            <a href="{{ route('super-admin.settings.index') }}" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-700 transition-colors">
                                <i class="fas fa-cog text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-900">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-900/50 border border-green-700 p-4 backdrop-blur">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-300">{{ session('success') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 rounded-lg bg-red-900/50 border border-red-700 p-4 backdrop-blur">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-300">{{ session('error') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 rounded-lg bg-yellow-900/50 border border-yellow-700 p-4 backdrop-blur">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-300">{{ session('warning') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-yellow-400 hover:text-yellow-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 rounded-lg bg-sa-900/50 border border-sa-700 p-4 backdrop-blur">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-sa-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-sa-300">{{ session('info') }}</p>
                            </div>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-sa-400 hover:text-sa-300">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-slate-800 border-t border-slate-700 py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between text-sm text-slate-500">
                    <p>&copy; {{ date('Y') }} Pharma ERP - <span class="text-sa-400 font-medium">Platform Control Panel</span></p>
                    <p class="mt-2 md:mt-0">Enterprise Edition v2.0 | <span class="text-green-400"><i class="fas fa-circle text-xs mr-1"></i>All Systems Operational</span></p>
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
            const flashMessages = document.querySelectorAll('[class*="rounded-lg bg-"][class*="border"][class*="p-4"]');
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
