@extends('layouts.app')

@section('title', 'Product Catalog')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Product Catalog</h1>
        <div class="flex space-x-4">
            <a href="{{ route('cart.index') }}" class="flex items-center text-gray-700 hover:text-gray-900">
                <svg class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Cart
            </a>
            <a href="{{ route('wishlist.index') }}" class="flex items-center text-gray-700 hover:text-gray-900">
                <svg class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Wishlist
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 rounded-md bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($products->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products available</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later for new products.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gray-200 relative">
                        @if($product->image)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        @if($product->stock < 1)
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="text-white font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ Str::limit($product->description, 60) }}</p>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                {{-- Price with MRP and Discount display --}}
                                @if($product->mrp > $product->price)
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 text-sm line-through">₹{{ number_format($product->mrp, 2) }}</span>
                                        <span class="text-xl font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                    </div>
                                    <span class="text-red-500 text-xs font-semibold">{{ $product->discount_percentage }}% OFF</span>
                                @else
                                    <span class="text-xl font-bold text-gray-900">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">Stock: {{ $product->stock }}</span>
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Add to Cart
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @endif
                            
                            <form action="{{ route('wishlist.add', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 flex items-center justify-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Add to Wishlist
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
