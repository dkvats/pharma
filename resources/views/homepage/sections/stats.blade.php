{{-- ===================== PLATFORM STATISTICS SECTION ===================== --}}
@php
    // Pull dynamic stats passed from HomeController via cache
    $s = $stats ?? [];

    // Number formatting helper (PHP side for data-raw attribute)
    $fmt = function(int $n): string {
        if ($n >= 1_000_000) {
            return round($n / 1_000_000, 1) . 'M+';
        }
        if ($n >= 1_000) {
            return number_format(round($n / 1_000, 1)) . 'K+';
        }
        return number_format($n) . '+';
    };

    // Stat cards — icon, raw value, formatted label, colors
    $statCards = [
        [
            'icon'      => 'fa-user-md',
            'raw'       => $s['statsDoctors']  ?? 0,
            'display'   => ($s['statsDoctors']  ?? 0) >= 1000
                               ? $fmt($s['statsDoctors']  ?? 0)
                               : number_format($s['statsDoctors']  ?? 0) . '+',
            'label'     => 'Verified Doctors',
            'color'     => 'text-blue-600',
            'bg'        => 'bg-blue-50',
            'border'    => 'border-blue-100',
        ],
        [
            'icon'      => 'fa-store',
            'raw'       => $s['statsStores']   ?? 0,
            'display'   => ($s['statsStores']   ?? 0) >= 1000
                               ? $fmt($s['statsStores']   ?? 0)
                               : number_format($s['statsStores']   ?? 0) . '+',
            'label'     => 'Registered Stores',
            'color'     => 'text-green-600',
            'bg'        => 'bg-green-50',
            'border'    => 'border-green-100',
        ],
        [
            'icon'      => 'fa-pills',
            'raw'       => $s['statsProducts'] ?? 0,
            'display'   => ($s['statsProducts'] ?? 0) >= 1000
                               ? $fmt($s['statsProducts'] ?? 0)
                               : number_format($s['statsProducts'] ?? 0) . '+',
            'label'     => 'Products Available',
            'color'     => 'text-purple-600',
            'bg'        => 'bg-purple-50',
            'border'    => 'border-purple-100',
        ],
        [
            'icon'      => 'fa-map-marker-alt',
            'raw'       => $s['statsStates']   ?? 0,
            'display'   => number_format($s['statsStates'] ?? 0),
            'label'     => 'States Covered',
            'color'     => 'text-orange-600',
            'bg'        => 'bg-orange-50',
            'border'    => 'border-orange-100',
        ],
        [
            'icon'      => 'fa-handshake',
            'raw'       => $s['statsOrders']   ?? 0,
            'display'   => ($s['statsOrders']   ?? 0) >= 1000
                               ? $fmt($s['statsOrders']   ?? 0)
                               : number_format($s['statsOrders']   ?? 0) . '+',
            'label'     => 'Orders Delivered',
            'color'     => 'text-teal-600',
            'bg'        => 'bg-teal-50',
            'border'    => 'border-teal-100',
        ],
        [
            'icon'      => 'fa-award',
            'raw'       => $s['statsYears']    ?? 15,
            'display'   => ($s['statsYears']    ?? 15) . '+',
            'label'     => 'Years Experience',
            'color'     => 'text-red-600',
            'bg'        => 'bg-red-50',
            'border'    => 'border-red-100',
        ],
    ];

    // Section heading from CMS or defaults
    $sectionContent = $content['stats'] ?? [];
    $title    = $sectionContent['title']    ?? 'Our Platform in Numbers';
    $subtitle = $sectionContent['subtitle'] ?? 'Trusted by veterinary professionals across India';
@endphp

<section id="stats" class="py-16 md:py-20 bg-gradient-to-br from-gray-50 to-green-50/30 relative overflow-hidden">

    {{-- Decorative blobs --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute top-0 left-1/4 w-72 h-72 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-25"></div>
        <div class="absolute bottom-0 right-1/4 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-25"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        {{-- Section Header --}}
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                {{ $title }}
            </h2>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                {{ $subtitle }}
            </p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5 md:gap-6">
            @foreach($statCards as $stat)
                <div class="group text-center p-5 md:p-6 bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 border {{ $stat['border'] }}">

                    {{-- Icon circle --}}
                    <div class="w-14 h-14 md:w-16 md:h-16 {{ $stat['bg'] }} rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas {{ $stat['icon'] }} {{ $stat['color'] }} text-xl md:text-2xl" aria-hidden="true"></i>
                    </div>

                    {{-- Animated number --}}
                    <div class="text-2xl md:text-3xl lg:text-4xl font-black text-gray-900 mb-1.5 leading-none stat-counter"
                         data-target="{{ $stat['raw'] }}"
                         data-display="{{ $stat['display'] }}"
                         aria-label="{{ $stat['raw'] }} {{ $stat['label'] }}">
                        {{ $stat['display'] }}
                    </div>

                    {{-- Label --}}
                    <div class="text-xs md:text-sm text-gray-500 font-medium leading-tight">
                        {{ $stat['label'] }}
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Trust badges row --}}
        <div class="mt-12 md:mt-14 flex flex-wrap justify-center items-center gap-x-8 gap-y-4">
            @foreach([
                ['fa-shield-alt',  'ISO Certified'],
                ['fa-certificate', 'GMP Compliant'],
                ['fa-check-circle','Quality Assured'],
                ['fa-truck',       'Pan India Delivery'],
            ] as [$icon, $label])
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas {{ $icon }} text-green-600 text-lg" aria-hidden="true"></i>
                    <span class="text-sm font-medium">{{ $label }}</span>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ===================== STATS COUNTER ANIMATION ===================== --}}
<script>
(function () {
    'use strict';

    // Easing: ease-out quad
    function easeOut(t) {
        return t * (2 - t);
    }

    // Format a plain integer the same way PHP does for the display value
    function formatNumber(raw, displayTemplate) {
        // If display template ends with "K+" or "M+", keep the suffix
        // We animate up to the raw integer, then swap to the formatted display at the end.
        if (displayTemplate.indexOf('K') !== -1) {
            var k = raw / 1000;
            return (k % 1 === 0 ? k.toFixed(0) : k.toFixed(1)) + 'K+';
        }
        if (displayTemplate.indexOf('M') !== -1) {
            var m = raw / 1000000;
            return (m % 1 === 0 ? m.toFixed(0) : m.toFixed(1)) + 'M+';
        }
        // Plain number — add thousands separator
        var s = Math.round(raw).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        // If the display has a trailing "+", keep it
        return displayTemplate.charAt(displayTemplate.length - 1) === '+'
            ? s + '+'
            : s;
    }

    function animateCounter(el) {
        var raw      = parseInt(el.dataset.target, 10) || 0;
        var display  = el.dataset.display;
        var duration = 1500; // ms
        var start    = null;

        if (raw === 0) {
            el.textContent = display;
            return;
        }

        function step(timestamp) {
            if (!start) start = timestamp;
            var elapsed  = timestamp - start;
            var progress = Math.min(elapsed / duration, 1);
            var eased    = easeOut(progress);
            var current  = Math.floor(eased * raw);

            el.textContent = formatNumber(current, display);

            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                // Snap to final formatted value
                el.textContent = display;
            }
        }

        requestAnimationFrame(step);
    }

    // Trigger animation when stats section enters the viewport
    function initCounters() {
        var counters = document.querySelectorAll('.stat-counter');
        if (!counters.length) return;

        if ('IntersectionObserver' in window) {
            var observed = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });

            counters.forEach(function (el) { observed.observe(el); });
        } else {
            // Fallback: animate immediately for browsers without IntersectionObserver
            counters.forEach(animateCounter);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCounters);
    } else {
        initCounters();
    }
})();
</script>
