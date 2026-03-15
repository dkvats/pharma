{{-- ===================== CTA SECTION ===================== --}}
@php $c = $content['cta'] ?? []; @endphp
<section id="cta" class="py-24 bg-gradient-to-r from-blue-700 to-indigo-700">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-extrabold text-white mb-5">
            {{ $c['title'] ?? 'Ready to Get Started?' }}
        </h2>
        <p class="text-xl text-blue-100 mb-10 leading-relaxed">
            {{ $c['subtitle'] ?? 'Login to your account or register to join the platform.' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-8 py-4 bg-white text-blue-800 text-lg font-bold rounded-xl hover:bg-blue-50 transition-all shadow-xl">
                    <i class="fas fa-tachometer-alt mr-3"></i>Go to Dashboard
                </a>
            @else
                <a href="{{ $c['button_link'] ?? route('login') }}"
                   class="inline-flex items-center px-8 py-4 bg-white text-blue-800 text-lg font-bold rounded-xl hover:bg-blue-50 transition-all shadow-xl">
                    <i class="fas fa-sign-in-alt mr-3"></i>{{ $c['button_text'] ?? 'Login Now' }}
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center px-8 py-4 border-2 border-white/60 text-white text-lg font-semibold rounded-xl hover:bg-white/10 transition-all">
                    <i class="fas fa-user-plus mr-3"></i>Create Account
                </a>
            @endauth
        </div>
    </div>
</section>
