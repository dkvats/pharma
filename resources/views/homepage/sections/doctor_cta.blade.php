{{-- ===================== DOCTOR / STORE CTA SECTION ===================== --}}
@php $dc = $content['doctor_cta'] ?? []; @endphp

<section id="doctor-cta" class="py-20 bg-gradient-to-br from-green-700 via-emerald-700 to-teal-700 relative overflow-hidden">

    {{-- Background decoration --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-green-400 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-teal-400 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-green-300/30 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center mb-14">
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white mb-4 leading-tight">
                {{ $dc['title'] ?? 'Join Our Network' }}
            </h2>
            <p class="text-xl text-green-200 max-w-2xl mx-auto">
                {{ $dc['subtitle'] ?? 'Register as a veterinary doctor or store and become part of our growing network.' }}
            </p>
        </div>

        {{-- Two CTA cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">

            {{-- Doctor Card --}}
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-3xl p-8 text-center hover:bg-white/15 transition-all group">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-white/30 transition-all">
                    <i class="fas fa-user-md text-white text-4xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-white mb-3">Veterinary Doctor</h3>
                <ul class="text-green-200 text-sm space-y-2 mb-6 text-left max-w-xs mx-auto">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Earn rewards on referrals</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Track monthly targets</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Spin & Win prizes</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Access leaderboard rankings</li>
                </ul>
                <a href="{{ route('doctor.register.form') }}"
                   class="inline-flex items-center justify-center gap-2 w-full px-6 py-3.5 bg-white text-green-700 font-bold text-base rounded-xl hover:bg-green-50 transition-all shadow-xl">
                    <i class="fas fa-user-md"></i>
                    {{ $dc['doctor_button'] ?? 'Register as Doctor' }}
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>

            {{-- Store Card --}}
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-3xl p-8 text-center hover:bg-white/15 transition-all group">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-white/30 transition-all">
                    <i class="fas fa-store text-white text-4xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-white mb-3">Pharmacy Store</h3>
                <ul class="text-green-200 text-sm space-y-2 mb-6 text-left max-w-xs mx-auto">
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Manage inventory online</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Receive doctor orders</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Access exclusive offers</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check-circle text-green-300"></i> Real-time stock tracking</li>
                </ul>
                <a href="{{ route('store.register.form') }}"
                   class="inline-flex items-center justify-center gap-2 w-full px-6 py-3.5 bg-green-400 text-white font-bold text-base rounded-xl hover:bg-green-300 transition-all shadow-xl">
                    <i class="fas fa-store"></i>
                    {{ $dc['store_button'] ?? 'Register as Store' }}
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        {{-- Bottom login hint --}}
        <div class="text-center mt-10">
            <p class="text-green-300 text-sm">
                Already registered?
                <a href="{{ route('login') }}" class="text-white font-bold underline underline-offset-2 hover:text-green-200 transition-colors">Login here</a>
            </p>
        </div>
    </div>
</section>
