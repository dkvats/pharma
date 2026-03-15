{{-- ===================== HERO SECTION - VETERINARY PHARMA ===================== --}}
@php $h = $content['hero'] ?? []; @endphp

<section id="hero" class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden pt-16 wave-divider">

    {{-- Background decorative elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-emerald-400 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-teal-400 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-green-300 rounded-full opacity-5 blur-3xl"></div>
        {{-- Decorative animal silhouettes / leaf pattern --}}
        <div class="absolute top-10 right-10 text-green-300 opacity-10 text-9xl">
            <i class="fas fa-leaf"></i>
        </div>
        <div class="absolute bottom-20 left-10 text-green-300 opacity-10 text-8xl">
            <i class="fas fa-seedling"></i>
        </div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Text Content --}}
            <div>
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 bg-green-400/20 border border-green-300/30 rounded-full px-4 py-2 mb-6">
                    <i class="fas fa-certificate text-green-300 text-sm"></i>
                    <span class="text-green-200 text-sm font-medium">Certified Veterinary Pharmaceuticals</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6 drop-shadow-lg">
                    {{ $h['title'] ?? 'Premium Veterinary Medicines for Healthy Livestock' }}
                </h1>
                <p class="text-lg sm:text-xl text-green-100 max-w-xl mb-10 leading-relaxed">
                    {{ $h['subtitle'] ?? 'Trusted pharmaceutical solutions for cattle, buffalo, goat, and poultry. Quality medicines delivered to your doorstep.' }}
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white text-green-800 text-lg font-bold rounded-xl hover:bg-green-50 transition-all shadow-xl">
                            <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ $h['button_link'] ?? '#products' }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white text-green-800 text-lg font-bold rounded-xl hover:bg-green-50 transition-all shadow-xl">
                            <i class="fas fa-flask mr-3"></i>{{ $h['button_text'] ?? 'Explore Products' }}
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white/60 text-white text-lg font-semibold rounded-xl hover:bg-white/10 transition-all">
                            <i class="fas fa-user-plus mr-3"></i>Join as Doctor / Store
                        </a>
                    @endauth
                </div>

                {{-- Trust indicators --}}
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-green-200">
                        <i class="fas fa-check-circle text-green-300"></i>
                        <span class="text-sm font-medium">Quality Assured</span>
                    </div>
                    <div class="flex items-center gap-2 text-green-200">
                        <i class="fas fa-truck text-green-300"></i>
                        <span class="text-sm font-medium">Pan-India Delivery</span>
                    </div>
                    <div class="flex items-center gap-2 text-green-200">
                        <i class="fas fa-user-md text-green-300"></i>
                        <span class="text-sm font-medium">Doctor Recommended</span>
                    </div>
                    <div class="flex items-center gap-2 text-green-200">
                        <i class="fas fa-shield-alt text-green-300"></i>
                        <span class="text-sm font-medium">100% Authentic</span>
                    </div>
                </div>
            </div>

            {{-- Right: Hero Image or Illustration --}}
            <div class="flex justify-center lg:justify-end">
                @if(!empty($h['image']))
                    <div class="relative">
                        <div class="absolute -inset-4 bg-green-400 rounded-3xl opacity-20 blur-xl"></div>
                        <img src="{{ asset('storage/' . $h['image']) }}" alt="Veterinary Medicines"
                             class="relative rounded-3xl shadow-2xl w-full max-w-lg object-cover max-h-96 border-4 border-white/20">
                    </div>
                @else
                    {{-- Placeholder illustration when no image --}}
                    <div class="relative">
                        <div class="absolute -inset-4 bg-green-400 rounded-full opacity-20 blur-3xl"></div>
                        <div class="relative w-80 h-80 bg-green-400/20 rounded-3xl border-2 border-green-300/30 flex items-center justify-center backdrop-blur-sm">
                            <div class="text-center">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="w-28 h-28 bg-green-400/30 rounded-2xl flex flex-col items-center justify-center border border-green-300/30">
                                        <i class="fas fa-cow text-4xl text-green-200 mb-1"></i>
                                        <span class="text-green-200 text-xs font-medium">Cattle</span>
                                    </div>
                                    <div class="w-28 h-28 bg-teal-400/30 rounded-2xl flex flex-col items-center justify-center border border-teal-300/30">
                                        <i class="fas fa-horse text-4xl text-teal-200 mb-1"></i>
                                        <span class="text-teal-200 text-xs font-medium">Livestock</span>
                                    </div>
                                    <div class="w-28 h-28 bg-emerald-400/30 rounded-2xl flex flex-col items-center justify-center border border-emerald-300/30">
                                        <i class="fas fa-feather-alt text-4xl text-emerald-200 mb-1"></i>
                                        <span class="text-emerald-200 text-xs font-medium">Poultry</span>
                                    </div>
                                    <div class="w-28 h-28 bg-green-500/30 rounded-2xl flex flex-col items-center justify-center border border-green-400/30">
                                        <i class="fas fa-pills text-4xl text-green-200 mb-1"></i>
                                        <span class="text-green-200 text-xs font-medium">Medicines</span>
                                    </div>
                                </div>
                                <p class="text-green-300 text-sm font-medium">Upload hero image from CMS</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Stats bar --}}
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <div class="text-3xl font-black text-white mb-1">500+</div>
                <div class="text-green-200 text-sm">Products</div>
            </div>
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <div class="text-3xl font-black text-white mb-1">2000+</div>
                <div class="text-green-200 text-sm">Doctors</div>
            </div>
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <div class="text-3xl font-black text-white mb-1">28</div>
                <div class="text-green-200 text-sm">States</div>
            </div>
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <div class="text-3xl font-black text-white mb-1">15+</div>
                <div class="text-green-200 text-sm">Years Exp.</div>
            </div>
        </div>
    </div>

    {{-- Scroll arrow --}}
    <div class="absolute bottom-20 left-1/2 -translate-x-1/2 animate-bounce">
        <a href="#products" class="text-white/60 hover:text-white/90 transition-colors flex flex-col items-center gap-1">
            <span class="text-xs text-green-300 font-medium">Scroll Down</span>
            <i class="fas fa-chevron-down text-xl"></i>
        </a>
    </div>
</section>
