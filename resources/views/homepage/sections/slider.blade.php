{{-- ===================== PRODUCTION-READY HOMEPAGE SLIDER ===================== --}}
@php
    $slideList = $slides ?? collect();
    $hasSlides = $slideList->where('status', 'active')->count() > 0;
    $activeSlides = $hasSlides ? $slideList->where('status', 'active')->sortBy('sort_order')->values() : collect();
    $slideCount = $activeSlides->count();

    // Local default veterinary images
    $defaultImages = [
        asset('images/slider/cow.jpg'),
        asset('images/slider/buffalo.jpg'),
        asset('images/slider/poultry.jpg'),
        asset('images/slider/goat.jpg'),
        asset('images/slider/veterinary.jpg'),
        asset('images/slider/farm.jpg'),
    ];

    // Get first slide image for preloading
    $firstSlideImage = null;
    if ($slideCount > 0) {
        $firstSlide = $activeSlides->first();
        if ($firstSlide->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($firstSlide->image)) {
            $firstSlideImage = $firstSlide->image_url;
        } else {
            $firstSlideImage = $defaultImages[0];
        }
    } else {
        $firstSlideImage = $defaultImages[0];
    }
@endphp

{{-- Preload First Slide Image (Performance Optimization) --}}
@if($firstSlideImage)
    <link rel="preload" as="image" href="{{ $firstSlideImage }}" fetchpriority="high">
@endif

{{-- Slider Container --}}
<section id="hero-slider" class="relative w-full overflow-hidden bg-gray-900" data-slide-count="{{ $slideCount }}" style="contain: layout style paint;">

    {{-- Fixed Height Container (Prevents CLS) --}}
    <div class="slider-container relative w-full h-[50vh] md:h-[70vh]" style="min-height: 300px;">

        @if($slideCount > 0)
            {{-- Render Active Slides --}}
            @foreach($activeSlides as $index => $slide)
                @php
                    $isFirst = $index === 0;
                    $imageUrl = null;

                    // Determine image source
                    if ($slide->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($slide->image)) {
                        $imageUrl = $slide->image_url;
                    } else {
                        $imageUrl = $defaultImages[$index % count($defaultImages)];
                    }
                @endphp

                {{-- Individual Slide --}}
                <div class="slide absolute inset-0 w-full h-full transition-opacity duration-700 ease-out {{ $isFirst ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                     data-index="{{ $index }}"
                     data-slide-id="{{ $slide->id }}"
                     style="will-change: opacity;">

                    {{-- Background Image with Object Cover --}}
                    <div class="absolute inset-0 w-full h-full bg-gray-800">
                        <img src="{{ $imageUrl }}"
                             alt="{{ $slide->title ?: 'Slide ' . ($index + 1) }}"
                             class="w-full h-full object-cover object-center"
                             loading="{{ $isFirst ? 'eager' : 'lazy' }}"
                             decoding="{{ $isFirst ? 'sync' : 'async' }}"
                             fetchpriority="{{ $isFirst ? 'high' : 'low' }}"
                             style="object-position: center;">

                        {{-- Dark Gradient Overlay for Text Readability --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-black/75 via-black/50 to-black/40" aria-hidden="true"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-black/30" aria-hidden="true"></div>
                    </div>

                    {{-- Content Container --}}
                    <div class="relative z-10 flex items-center justify-center h-full px-4 sm:px-6 lg:px-8 xl:px-12">
                        <div class="max-w-5xl mx-auto text-center">

                            {{-- Animated Title --}}
                            @if($slide->title)
                                <h2 class="slide-title text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold text-white leading-tight mb-4 md:mb-6 drop-shadow-2xl transform transition-all duration-700 {{ $isFirst ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' }}">
                                    {{ $slide->title }}
                                </h2>
                            @endif

                            {{-- Animated Subtitle --}}
                            @if($slide->subtitle)
                                <p class="slide-subtitle text-lg sm:text-xl md:text-2xl lg:text-3xl text-white/95 max-w-4xl mx-auto mb-6 md:mb-8 leading-relaxed drop-shadow-lg transform transition-all duration-700 delay-100 {{ $isFirst ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' }}">
                                    {{ $slide->subtitle }}
                                </p>
                            @endif

                            {{-- Animated Button --}}
                            @if($slide->button_text && $slide->button_link)
                                <div class="slide-button transform transition-all duration-700 delay-200 {{ $isFirst ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0' }}">
                                    <a href="{{ $slide->button_link }}"
                                       class="inline-flex items-center gap-2 px-6 py-3 md:px-8 md:py-4 bg-green-600 hover:bg-green-500 text-white font-semibold text-base md:text-lg rounded-full transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 group">
                                        {{ $slide->button_text }}
                                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform duration-200" aria-hidden="true"></i>
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach

        @else
            {{-- Default Fallback Slide when no slides configured --}}
            <div class="slide absolute inset-0 w-full h-full" data-index="0">
                <div class="absolute inset-0 w-full h-full bg-gray-800">
                    <img src="{{ $defaultImages[0] }}"
                         alt="Veterinary Care"
                         class="w-full h-full object-cover object-center"
                         loading="eager"
                         decoding="sync"
                         fetchpriority="high">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-900/85 via-green-800/70 to-emerald-900/75" aria-hidden="true"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/40" aria-hidden="true"></div>
                </div>

                <div class="relative z-10 flex items-center justify-center h-full px-4 sm:px-6 lg:px-8">
                    <div class="max-w-4xl mx-auto text-center">
                        <div class="w-20 h-20 md:w-24 md:h-24 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 border border-white/20">
                            <i class="fas fa-heartbeat text-white text-3xl md:text-4xl" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4 md:mb-6 drop-shadow-2xl">
                            {{ $settings->site_name ?? 'VetPharma India' }}
                        </h2>
                        <p class="text-lg sm:text-xl md:text-2xl text-white/95 max-w-3xl mx-auto mb-6 md:mb-8 leading-relaxed drop-shadow-lg">
                            Premium veterinary medicines and healthcare solutions for cattle, buffalo, goat, and poultry across India.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 md:px-8 md:py-4 bg-white text-green-800 font-semibold text-base md:text-lg rounded-full transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                                Get Started
                            </a>
                            <a href="#products"
                               class="inline-flex items-center justify-center gap-2 px-6 py-3 md:px-8 md:py-4 bg-green-600/90 backdrop-blur-sm text-white font-semibold text-base md:text-lg rounded-full border border-white/30 transition-all duration-300 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400">
                                <i class="fas fa-flask" aria-hidden="true"></i>
                                Explore Products
                            </a>
                        </div>
                        <p class="text-green-200/90 text-xs md:text-sm mt-8">
                            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                            Admin can add custom slides from <strong>Admin Panel → Homepage Slides</strong>
                        </p>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Navigation Arrows (only show if multiple slides) --}}
    @if($slideCount > 1)
        <button type="button"
                id="slider-prev"
                class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-white/10 backdrop-blur-md hover:bg-white/25 text-white rounded-full flex items-center justify-center transition-all duration-300 border border-white/20 hover:border-white/50 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Previous slide">
            <i class="fas fa-chevron-left text-lg md:text-xl" aria-hidden="true"></i>
        </button>

        <button type="button"
                id="slider-next"
                class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 z-20 w-12 h-12 md:w-14 md:h-14 bg-white/10 backdrop-blur-md hover:bg-white/25 text-white rounded-full flex items-center justify-center transition-all duration-300 border border-white/20 hover:border-white/50 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Next slide">
            <i class="fas fa-chevron-right text-lg md:text-xl" aria-hidden="true"></i>
        </button>
    @endif

    {{-- Dot Indicators (only show if multiple slides) --}}
    @if($slideCount > 1)
        <div class="absolute bottom-6 md:bottom-10 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2 md:gap-3">
            @foreach($activeSlides as $index => $slide)
                <button type="button"
                        class="slider-dot w-2.5 h-2.5 md:w-3 md:h-3 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-white/50 {{ $index === 0 ? 'bg-white w-6 md:w-8' : 'bg-white/50 hover:bg-white/70' }}"
                        data-index="{{ $index }}"
                        aria-label="Go to slide {{ $index + 1 }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}">
                </button>
            @endforeach
        </div>
    @endif

    {{-- Slide Counter (top right) --}}
    @if($slideCount > 1)
        <div class="absolute top-4 md:top-6 right-4 md:right-6 z-20 bg-black/40 backdrop-blur-sm text-white text-xs md:text-sm font-medium px-3 py-1.5 md:px-4 md:py-2 rounded-full border border-white/10">
            <span id="slide-current">1</span> / <span id="slide-total">{{ $slideCount }}</span>
        </div>
    @endif

    {{-- Scroll Down Indicator --}}
    <div class="absolute bottom-20 md:bottom-24 left-1/2 -translate-x-1/2 z-20 animate-bounce hidden md:flex flex-col items-center">
        <a href="#products" class="text-white/70 hover:text-white transition-colors flex flex-col items-center gap-1 focus:outline-none focus:ring-2 focus:ring-white/50 rounded-lg p-2">
            <span class="text-[10px] font-medium tracking-wider uppercase">Scroll</span>
            <i class="fas fa-chevron-down text-sm" aria-hidden="true"></i>
        </a>
    </div>

</section>

{{-- Slider Styles --}}
<style>
/* Prevent layout shift - fixed aspect ratio container */
#hero-slider {
    aspect-ratio: 16 / 9;
    max-height: 70vh;
}

@media (max-width: 768px) {
    #hero-slider {
        aspect-ratio: auto;
        max-height: 50vh;
    }
}

/* Smooth slide transitions */
.slide {
    backface-visibility: hidden;
    transform: translateZ(0);
}

/* Content animations when slide becomes active */
.slide.active .slide-title,
.slide.active .slide-subtitle,
.slide.active .slide-button {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Dot indicator active state */
.slider-dot.active {
    background-color: white !important;
    width: 1.5rem !important;
}

@media (min-width: 768px) {
    .slider-dot.active {
        width: 2rem !important;
    }
}

/* Reduced motion preference */
@media (prefers-reduced-motion: reduce) {
    .slide,
    .slide-title,
    .slide-subtitle,
    .slide-button {
        transition: none !important;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .slide .bg-gradient-to-r,
    .slide .bg-gradient-to-t {
        opacity: 0.9;
    }
}
</style>

{{-- Slider JavaScript --}}
@if($slideCount > 0)
<script>
(function() {
    'use strict';

    const slider = document.getElementById('hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.slide');
    const dots = slider.querySelectorAll('.slider-dot');
    const prevBtn = document.getElementById('slider-prev');
    const nextBtn = document.getElementById('slider-next');
    const currentEl = document.getElementById('slide-current');

    const totalSlides = slides.length;
    if (totalSlides <= 1) return;

    let currentIndex = 0;
    let autoPlayTimer = null;
    const SLIDE_INTERVAL = 4000;
    let isPaused = false;
    let isVisible = true;

    // Initialize
    function init() {
        // Mark first slide as active for CSS animations
        slides[0].classList.add('active');
        startAutoPlay();
        attachEvents();

        // Use Intersection Observer for performance
        observeVisibility();
    }

    // Intersection Observer to pause when not visible
    function observeVisibility() {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    isVisible = entry.isIntersecting;
                    if (isVisible && !isPaused) {
                        startAutoPlay();
                    } else {
                        stopAutoPlay();
                    }
                });
            }, { threshold: 0.5 });

            observer.observe(slider);
        }
    }

    // Go to specific slide
    function goToSlide(index) {
        // Normalize index
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        currentIndex = index;

        // Update slides
        slides.forEach((slide, i) => {
            if (i === currentIndex) {
                slide.classList.add('active');
                slide.classList.remove('opacity-0', 'z-0');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.remove('active');
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'z-0');
            }
        });

        // Update dots
        dots.forEach((dot, i) => {
            if (i === currentIndex) {
                dot.classList.add('active');
                dot.setAttribute('aria-current', 'true');
                dot.classList.remove('bg-white/50');
                dot.classList.add('bg-white');
            } else {
                dot.classList.remove('active');
                dot.setAttribute('aria-current', 'false');
                dot.classList.remove('bg-white');
                dot.classList.add('bg-white/50');
            }
        });

        // Update counter
        if (currentEl) {
            currentEl.textContent = currentIndex + 1;
        }
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    // Auto-play
    function startAutoPlay() {
        stopAutoPlay();
        if (!isPaused && isVisible) {
            autoPlayTimer = setInterval(nextSlide, SLIDE_INTERVAL);
        }
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    // Event handlers
    function attachEvents() {
        // Navigation buttons
        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                stopAutoPlay();
                prevSlide();
                startAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                stopAutoPlay();
                nextSlide();
                startAutoPlay();
            });
        }

        // Dot indicators
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                const index = parseInt(this.dataset.index, 10);
                stopAutoPlay();
                goToSlide(index);
                startAutoPlay();
            });
        });

        // Pause on hover
        slider.addEventListener('mouseenter', function() {
            isPaused = true;
            stopAutoPlay();
        });

        slider.addEventListener('mouseleave', function() {
            isPaused = false;
            if (isVisible) {
                startAutoPlay();
            }
        });

        // Touch/Swipe support
        let touchStartX = 0;
        let touchEndX = 0;

        slider.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        slider.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > 50) {
                stopAutoPlay();
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
                startAutoPlay();
            }
        }, { passive: true });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                stopAutoPlay();
                prevSlide();
                startAutoPlay();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                stopAutoPlay();
                nextSlide();
                startAutoPlay();
            }
        });

        // Page Visibility API
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoPlay();
            } else if (!isPaused && isVisible) {
                startAutoPlay();
            }
        });
    }

    // Start
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endif
