{{-- ===================== ANIMAL CATEGORIES SECTION ===================== --}}
@php $a = $content['animals'] ?? []; @endphp

<section id="animals" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-teal-100 text-teal-700 rounded-full px-4 py-1.5 text-sm font-semibold mb-4">
                <i class="fas fa-paw text-teal-500"></i>
                Animal Categories
            </div>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4">
                {{ $a['title'] ?? 'Medicines for All Animals' }}
            </h2>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                {{ $a['subtitle'] ?? 'Specialized healthcare solutions for every livestock category.' }}
            </p>
        </div>

        {{-- Animal Cards Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Cow --}}
            <a href="{{ route('login') }}" class="animal-card rounded-3xl p-8 flex flex-col items-center text-center cursor-pointer group">
                <div class="animal-icon w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/20 transition-all">
                    @if(!empty($a['cow_image']))
                        <img src="{{ asset('storage/' . $a['cow_image']) }}" alt="{{ $a['cow_label'] ?? 'Cow' }}"
                             class="w-full h-full object-cover rounded-full">
                    @else
                        <i class="fas fa-cow text-5xl text-green-600 group-hover:text-white transition-colors"></i>
                    @endif
                </div>
                <h3 class="animal-label text-xl font-bold text-gray-800 mb-2 transition-colors">{{ $a['cow_label'] ?? 'Cow' }}</h3>
                <p class="text-sm text-gray-500 group-hover:text-green-200 transition-colors">Bovine Medicines & Supplements</p>
                <div class="mt-4 w-8 h-0.5 bg-green-400 group-hover:bg-white transition-colors mx-auto"></div>
            </a>

            {{-- Buffalo --}}
            <a href="{{ route('login') }}" class="animal-card rounded-3xl p-8 flex flex-col items-center text-center cursor-pointer group">
                <div class="animal-icon w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/20 transition-all">
                    @if(!empty($a['buffalo_image']))
                        <img src="{{ asset('storage/' . $a['buffalo_image']) }}" alt="{{ $a['buffalo_label'] ?? 'Buffalo' }}"
                             class="w-full h-full object-cover rounded-full">
                    @else
                        <i class="fas fa-hippo text-5xl text-green-600 group-hover:text-white transition-colors"></i>
                    @endif
                </div>
                <h3 class="animal-label text-xl font-bold text-gray-800 mb-2 transition-colors">{{ $a['buffalo_label'] ?? 'Buffalo' }}</h3>
                <p class="text-sm text-gray-500 group-hover:text-green-200 transition-colors">Buffalo Health & Nutrition</p>
                <div class="mt-4 w-8 h-0.5 bg-green-400 group-hover:bg-white transition-colors mx-auto"></div>
            </a>

            {{-- Goat --}}
            <a href="{{ route('login') }}" class="animal-card rounded-3xl p-8 flex flex-col items-center text-center cursor-pointer group">
                <div class="animal-icon w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/20 transition-all">
                    @if(!empty($a['goat_image']))
                        <img src="{{ asset('storage/' . $a['goat_image']) }}" alt="{{ $a['goat_label'] ?? 'Goat' }}"
                             class="w-full h-full object-cover rounded-full">
                    @else
                        <svg class="w-16 h-16 text-green-600 group-hover:text-white transition-colors" fill="currentColor" viewBox="0 0 100 100">
                            <ellipse cx="50" cy="65" rx="28" ry="22"/>
                            <circle cx="50" cy="35" r="18"/>
                            <ellipse cx="36" cy="22" rx="5" ry="8" transform="rotate(-20 36 22)"/>
                            <ellipse cx="64" cy="22" rx="5" ry="8" transform="rotate(20 64 22)"/>
                        </svg>
                    @endif
                </div>
                <h3 class="animal-label text-xl font-bold text-gray-800 mb-2 transition-colors">{{ $a['goat_label'] ?? 'Goat' }}</h3>
                <p class="text-sm text-gray-500 group-hover:text-green-200 transition-colors">Caprine Care & Vaccines</p>
                <div class="mt-4 w-8 h-0.5 bg-green-400 group-hover:bg-white transition-colors mx-auto"></div>
            </a>

            {{-- Poultry --}}
            <a href="{{ route('login') }}" class="animal-card rounded-3xl p-8 flex flex-col items-center text-center cursor-pointer group">
                <div class="animal-icon w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mb-4 group-hover:bg-white/20 transition-all">
                    @if(!empty($a['poultry_image']))
                        <img src="{{ asset('storage/' . $a['poultry_image']) }}" alt="{{ $a['poultry_label'] ?? 'Poultry' }}"
                             class="w-full h-full object-cover rounded-full">
                    @else
                        <i class="fas fa-feather-alt text-5xl text-green-600 group-hover:text-white transition-colors"></i>
                    @endif
                </div>
                <h3 class="animal-label text-xl font-bold text-gray-800 mb-2 transition-colors">{{ $a['poultry_label'] ?? 'Poultry' }}</h3>
                <p class="text-sm text-gray-500 group-hover:text-green-200 transition-colors">Poultry Medicines & Feed</p>
                <div class="mt-4 w-8 h-0.5 bg-green-400 group-hover:bg-white transition-colors mx-auto"></div>
            </a>
        </div>

        {{-- Bottom info bar --}}
        <div class="mt-12 bg-gradient-to-r from-green-50 to-teal-50 rounded-2xl p-6 border border-green-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">Can't find your animal category?</p>
                    <p class="text-gray-500 text-xs">We cover 20+ animal species with specialized medicine ranges.</p>
                </div>
            </div>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors flex-shrink-0">
                <i class="fas fa-search"></i> Browse All Categories
            </a>
        </div>

    </div>
</section>
