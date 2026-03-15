@extends('layouts.super-admin')
@section('title', 'Featured Products')
@section('page-subtitle', 'Choose which products appear on the homepage')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('super-admin.homepage-cms.index') }}" class="hover:text-white transition-colors">Homepage CMS</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-white">Featured Products</span>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-300 rounded-xl p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Currently Featured --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700">
        <div class="p-6 border-b border-slate-700">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fas fa-star text-yellow-400"></i>
                Currently Featured ({{ $featuredProducts->count() }})
            </h3>
            <p class="text-slate-400 text-sm mt-0.5">These products appear in the Featured Products section of the homepage.</p>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse($featuredProducts as $product)
                <div class="p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-700 rounded-xl overflow-hidden flex-shrink-0">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-pills text-slate-500"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">{{ $product->name }}</div>
                            <div class="text-slate-400 text-xs">
                                {{ $product->category ?? 'No category' }} &middot;
                                <span class="text-green-400">&#8377;{{ number_format($product->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('super-admin.homepage-cms.products.toggle', $product) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-500/20 text-red-400 text-xs font-semibold rounded-lg hover:bg-red-500/30 transition-colors">
                            <i class="fas fa-star-half-alt"></i> Remove from Homepage
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-star text-4xl mb-3 block text-slate-600"></i>
                    No products featured yet. Add some from the list below.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Available Products --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700">
        <div class="p-6 border-b border-slate-700">
            <h3 class="text-white font-bold text-lg flex items-center gap-2">
                <i class="fas fa-plus-circle text-green-400"></i>
                Add to Featured ({{ $allProducts->count() }} available)
            </h3>
            <p class="text-slate-400 text-sm mt-0.5">Click a product to feature it on the homepage. Maximum 8 products recommended.</p>
        </div>
        <div class="divide-y divide-slate-700">
            @forelse($allProducts as $product)
                <div class="p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-700 rounded-xl overflow-hidden flex-shrink-0">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-pills text-slate-500"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="text-white font-semibold text-sm">{{ $product->name }}</div>
                            <div class="text-slate-400 text-xs">
                                {{ $product->category ?? 'No category' }} &middot;
                                <span class="text-green-400">&#8377;{{ number_format($product->price, 2) }}</span>
                                &middot; Stock: {{ $product->stock }}
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('super-admin.homepage-cms.products.toggle', $product) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500/20 text-green-400 text-xs font-semibold rounded-lg hover:bg-green-500/30 transition-colors">
                            <i class="fas fa-star"></i> Feature on Homepage
                        </button>
                    </form>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3 block text-slate-600"></i>
                    All active products are already featured.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection