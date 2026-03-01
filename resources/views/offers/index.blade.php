@extends('layouts.app')

@section('title', 'My Offers')
@section('page-title', 'My Offers')
@section('page-description', 'Exclusive deals and discounts for you')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">My Offers</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">&larr; Back to Dashboard</a>
    </div>

    @if($dailyOffer)
    <!-- Offer of the Day -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg shadow-lg p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-2">
                    <i class="fas fa-fire mr-1"></i> Offer of the Day
                </span>
                <h2 class="text-2xl font-bold">{{ $dailyOffer->title }}</h2>
                <p class="text-white/90 mt-1">{{ $dailyOffer->description }}</p>
                <div class="mt-3 flex items-center gap-3">
                    <span class="text-3xl font-bold">{{ $dailyOffer->discount_display }}</span>
                    @if($dailyOffer->end_date)
                        <span class="text-sm bg-white/20 px-3 py-1 rounded-full">Ends {{ $dailyOffer->end_date->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('products.catalog') }}" class="inline-flex items-center px-6 py-3 bg-white text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition-colors">
                Shop Now
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
    @endif

    @if($ongoingOffers->count() > 0)
    <!-- Ongoing Offers -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-tags text-blue-500 mr-2"></i>Ongoing Offers
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ongoingOffers as $offer)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full mb-2">
                    {{ ucfirst($offer->offer_type) }}
                </span>
                <h3 class="font-semibold text-gray-900">{{ $offer->title }}</h3>
                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($offer->description, 80) }}</p>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-lg font-bold text-green-600">{{ $offer->discount_display }}</span>
                    <a href="{{ route('products.catalog') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Products <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
                @if($offer->start_date && $offer->end_date)
                    <p class="text-xs text-gray-400 mt-2">
                        Valid: {{ $offer->start_date->format('d M Y') }} - {{ $offer->end_date->format('d M Y') }}
                    </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <i class="fas fa-tags text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">No Active Offers</h3>
        <p class="text-gray-500 mt-2">Check back later for exciting deals and discounts!</p>
        <a href="{{ route('products.catalog') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Browse Products
        </a>
    </div>
    @endif
</div>
@endsection
