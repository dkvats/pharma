@extends('layouts.app')

@section('title', 'Available Offers')
@section('page-title', 'Store Offers')
@section('page-description', 'Exclusive offers for your store')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Available Offers</h1>
        <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fas fa-shopping-cart mr-2"></i>Place Order with Offer
        </a>
    </div>

    @if($offers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($offers as $offer)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow">
                    @if($offer->featured_image)
                        <div class="h-48 bg-gray-200">
                            <img src="{{ asset('storage/' . $offer->featured_image) }}" alt="{{ $offer->title }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <i class="fas fa-tags text-6xl text-white opacity-50"></i>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                Store Exclusive
                            </span>
                            @if($offer->offer_type === 'daily')
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                    Offer of the Day
                                </span>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $offer->title }}</h3>
                        
                        @if($offer->description)
                            <p class="text-gray-600 text-sm mb-4">{{ $offer->description }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-green-600 font-bold text-2xl">
                                @if($offer->discount_type === 'percentage')
                                    {{ $offer->discount_value }}% OFF
                                @else
                                    ₹{{ number_format($offer->discount_value, 0) }} OFF
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-500 mb-4">
                            <p><i class="fas fa-calendar-alt mr-2"></i>
                                Valid: {{ $offer->start_date?->format('d M Y') ?? 'Now' }} - {{ $offer->end_date?->format('d M Y') ?? 'Ongoing' }}
                            </p>
                        </div>
                        
                        <a href="{{ route('orders.create') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
                            Apply This Offer
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-800">How to Use Offers</h4>
                    <p class="text-blue-700 text-sm mt-1">
                        Click "Place Order with Offer" or "Apply This Offer" to go to the order page. 
                        Select your products and the offer will be automatically applied during checkout.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow p-12 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-tags text-6xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Active Offers</h3>
            <p class="text-gray-500 mb-6">There are no exclusive offers available for your store at the moment.</p>
            <a href="{{ route('orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                Continue Shopping
            </a>
        </div>
    @endif
</div>
@endsection
