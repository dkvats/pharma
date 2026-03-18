<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->site_name ?? 'VetPharma India' }} - {{ $settings->tagline ?? 'Quality Veterinary Medicines' }}</title>
    <meta name="description" content="{{ $content['hero']['subtitle'] ?? 'Premium veterinary pharmaceutical solutions for cattle, buffalo, goat and poultry.' }}">

    {{-- Favicon --}}
    @if(!empty($settings->favicon))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }

        /* Veterinary green/teal brand */
        :root {
            --vet-green: #16a34a;
            --vet-green-dark: #15803d;
            --vet-teal: #0d9488;
            --vet-green-light: #dcfce7;
        }

        /* Hero gradient - veterinary green */
        .hero-gradient {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 30%, #047857 60%, #059669 100%);
        }

        /* Sticky navbar */
        .nav-sticky { backdrop-filter: blur(12px); background: rgba(4, 120, 87, 0.95); }

        /* Cards hover effect */
        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover { transform: translateY(-6px); box-shadow: 0 24px 48px rgba(0,0,0,0.15); }

        /* Section divider wave */
        .wave-divider { position: relative; }
        .wave-divider::after {
            content: '';
            position: absolute;
            bottom: -1px; left: 0; right: 0;
            height: 60px;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 1440 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,30 C360,60 720,0 1080,30 C1260,45 1380,20 1440,30 L1440,60 L0,60 Z' fill='white'/%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        /* Product card */
        .product-card { transition: all 0.3s ease; }
        .product-card:hover { transform: translateY(-4px); }

        /* Animal category card */
        .animal-card {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #bbf7d0;
            transition: all 0.3s ease;
        }
        .animal-card:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            border-color: #16a34a;
            transform: translateY(-6px);
        }
        .animal-card:hover .animal-label { color: white; }
        .animal-card:hover .animal-icon { background: rgba(255,255,255,0.2); }

        /* Trust point card */
        .trust-card {
            background: white;
            border-left: 4px solid #16a34a;
            transition: all 0.3s ease;
        }
        .trust-card:hover { transform: translateX(6px); box-shadow: 0 10px 30px rgba(22,163,74,0.15); }

        /* Slider */
        .slider-container { overflow: hidden; }
        .slider-track { display: flex; gap: 1.5rem; animation: autoSlide 30s linear infinite; }
        .slider-track:hover { animation-play-state: paused; }
        @keyframes autoSlide {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        /* Stats counter */
        .stat-number { font-size: 2.5rem; font-weight: 900; color: #16a34a; }

        /* Mobile hamburger */
        #mobile-menu { display: none; }
        #mobile-menu.open { display: block; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased">

{{-- ===================== NAVBAR ===================== --}}
<nav class="nav-sticky fixed top-0 left-0 right-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo & Site Name --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                @if($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->site_name }}" class="h-10 w-auto object-contain">
                @else
                    <div class="w-10 h-10 bg-green-400 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-heartbeat text-white text-lg"></i>
                    </div>
                @endif
                <div>
                    <span class="text-white text-lg font-bold tracking-tight block leading-tight">{{ $settings->site_name ?? 'VetPharma India' }}</span>
                    @if($settings->tagline)
                        <span class="text-green-200 text-xs block leading-tight">{{ $settings->tagline }}</span>
                    @endif
                </div>
            </a>

            {{-- Dynamic Nav Links (desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                @if(isset($navItems) && $navItems->count() > 0)
                    @foreach($navItems as $item)
                        @if($item->status === 'active')
                            @if($item->is_external)
                                <a href="{{ $item->url }}" target="_blank" rel="noopener" class="text-green-100 hover:text-white text-sm font-medium transition-colors">
                                    {{ $item->label }} <i class="fas fa-external-link-alt text-xs ml-1"></i>
                                </a>
                            @else
                                <a href="{{ $item->url }}" class="text-green-100 hover:text-white text-sm font-medium transition-colors">{{ $item->label }}</a>
                            @endif
                        @endif
                    @endforeach
                @else
                    <a href="#hero" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Home</a>
                    <a href="#products" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Products</a>
                    <a href="#animals" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Animals</a>
                    <a href="#about" class="text-green-100 hover:text-white text-sm font-medium transition-colors">About</a>
                    <a href="#contact" class="text-green-100 hover:text-white text-sm font-medium transition-colors">Contact</a>
                @endif
            </div>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="hidden md:inline-flex items-center px-4 py-2 bg-white text-green-800 text-sm font-semibold rounded-lg hover:bg-green-50 transition-colors shadow">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 border border-white/40 text-white text-sm font-medium rounded-lg hover:bg-white/10 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="hidden sm:inline-flex items-center px-4 py-2 bg-green-400 text-white text-sm font-semibold rounded-lg hover:bg-green-300 transition-colors shadow-lg">
                        <i class="fas fa-user-plus mr-2"></i>Register
                    </a>
                @endauth

                {{-- Mobile menu button --}}
                <button onclick="document.getElementById('mobile-menu').classList.toggle('open')"
                        class="md:hidden text-white hover:text-green-200 ml-2">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="pb-4 border-t border-green-600 mt-1 pt-3">
            @if(isset($navItems) && $navItems->count() > 0)
                @foreach($navItems as $item)
                    @if($item->status === 'active')
                        <a href="{{ $item->url }}" @if($item->is_external) target="_blank" @endif
                           class="block py-2 text-green-100 hover:text-white text-sm font-medium">{{ $item->label }}</a>
                    @endif
                @endforeach
            @else
                <a href="#hero" class="block py-2 text-green-100 hover:text-white text-sm">Home</a>
                <a href="#products" class="block py-2 text-green-100 hover:text-white text-sm">Products</a>
                <a href="#about" class="block py-2 text-green-100 hover:text-white text-sm">About</a>
                <a href="#contact" class="block py-2 text-green-100 hover:text-white text-sm">Contact</a>
            @endif
            @guest
                <a href="{{ route('register') }}" class="block py-2 text-green-300 font-semibold text-sm">Register Free</a>
            @endguest
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
            {{ session('success') }}
        </div>
    </div>
@endif

{{-- ===================== IMAGE SLIDER (top of page, above all sections) ===================== --}}
@include('homepage.sections.slider', [
    'slides'   => $slides ?? collect(),
    'settings' => $settings,
])

{{-- ===================== DYNAMIC SECTIONS ===================== --}}
@foreach($sections as $sectionKey => $section)
    @if($section->status === 'active')
        @if(view()->exists('homepage.sections.' . $sectionKey))
            @include('homepage.sections.' . $sectionKey, [
                'content'         => $content,
                'settings'        => $settings,
                'features'        => $features,
                'featuredProducts' => $featuredProducts ?? collect(),
            ])
        @endif
    @endif
@endforeach

{{-- ===================== FOOTER ===================== --}}
@if(isset($sections['footer']) && $sections['footer']->status === 'active')
    @include('homepage.sections.footer', ['content' => $content, 'settings' => $settings])
@endif

<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Close mobile menu if open
                document.getElementById('mobile-menu').classList.remove('open');
            }
        });
    });

    // Sticky nav background transition
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('nav');
        if (window.scrollY > 50) {
            nav.style.background = 'rgba(4, 120, 87, 0.98)';
        } else {
            nav.style.background = 'rgba(4, 120, 87, 0.95)';
        }
    });
</script>

</body>
</html>
