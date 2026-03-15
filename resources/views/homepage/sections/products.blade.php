{{-- ===================== FEATURED PRODUCTS SECTION ===================== --}}
@php $p = $content['products'] ?? []; @endphp

<section id="products" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 rounded-full px-4 py-1.5 text-sm font-semibold mb-4">
                <i class="fas fa-star text-green-500"></i>
                Featured Products
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                {{ $p['title'] ?? 'Our Featured Products' }}
            </h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                {{ $p['subtitle'] ?? 'Trusted veterinary medicines for cattle, buffalo, and livestock health care.' }}
            </p>
        </div>

        {{-- Products Grid --}}
        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden product-card hover-lift group">
                        {{-- Product Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-green-50 to-emerald-100 overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-pills text-5xl text-green-300"></i>
                                </div>
                            @endif
                            {{-- Category badge --}}
                            @if($product->category)
                                <div class="absolute top-3 left-3 bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                    {{ $product->category }}
                                </div>
                            @endif
                            {{-- Prescription badge --}}
                            @if($product->requires_prescription)
                                <div class="absolute top-3 right-3 bg-amber-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                    <i class="fas fa-file-prescription mr-1"></i>Rx
                                </div>
                            @endif
                        </div>

                        {{-- Product Info --}}
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 text-sm mb-1 line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            @if($product->description)
                                <p class="text-gray-400 text-xs mb-3 line-clamp-2">{{ $product->description }}</p>
                            @endif
                            <div class="flex items-center justify-between">
                                <div>
                                    {{-- Price with MRP and Discount display --}}
                                    @if($product->mrp > $product->price)
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-400 text-sm line-through">₹{{ number_format($product->mrp, 2) }}</span>
                                            <span class="text-green-600 font-extrabold text-lg">₹{{ number_format($product->price, 2) }}</span>
                                        </div>
                                        <span class="text-red-500 text-xs font-semibold">{{ $product->discount_percentage }}% OFF</span>
                                    @else
                                        <span class="text-green-600 font-extrabold text-lg">₹{{ number_format($product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($product->stock > 0)
                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                            <i class="fas fa-circle text-[6px]"></i>In Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-medium px-2 py-0.5 rounded-full">
                                            <i class="fas fa-circle text-[6px]"></i>Out of Stock
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- View Product Button --}}
                            <a href="{{ route('login') }}"
                               class="mt-3 w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View Product
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All CTA --}}
            <div class="text-center mt-10">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center gap-2 px-8 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors shadow-lg">
                    <i class="fas fa-th-large"></i>
                    View All Products
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            {{-- Empty state --}}
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pills text-3xl text-green-400"></i>
                </div>
                <h3 class="text-gray-500 font-medium mb-2">No Featured Products Yet</h3>
                <p class="text-gray-400 text-sm">Super Admin can mark products as featured from the CMS dashboard.</p>
            </div>
        @endif
    </div>
</section>
