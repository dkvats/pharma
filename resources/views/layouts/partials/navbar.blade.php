<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-xl font-bold text-indigo-600">
                    Pharma Management
                </a>
            </div>

            <div class="flex items-center">
                @auth
                    <div class="flex items-center space-x-4">
                        @php
                            $user = auth()->user();
                            $canUseCart = $user->hasRole('Doctor') || $user->hasRole('End User');
                        @endphp

                        @if($canUseCart)
                            <!-- Cart Icon -->
                            <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-gray-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                @php
                                    $cartCount = $user->cart?->items()->sum('quantity') ?? 0;
                                @endphp
                                @if($cartCount > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                                @endif
                            </a>

                            <!-- Wishlist Icon -->
                            <a href="{{ route('wishlist.index') }}" class="relative text-gray-600 hover:text-gray-900">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                @php
                                    $wishlistCount = $user->wishlist?->items()->count() ?? 0;
                                @endphp
                                @if($wishlistCount > 0)
                                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $wishlistCount }}</span>
                                @endif
                            </a>
                        @endif

                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if(auth()->user()->hasRole('Admin')) bg-red-100 text-red-800
                            @elseif(auth()->user()->hasRole('Sub Admin')) bg-orange-100 text-orange-800
                            @elseif(auth()->user()->hasRole('Doctor')) bg-blue-100 text-blue-800
                            @elseif(auth()->user()->hasRole('Store')) bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ auth()->user()->roles->first()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                    <a href="{{ route('register') }}" class="ml-4 text-gray-700 hover:text-gray-900">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
