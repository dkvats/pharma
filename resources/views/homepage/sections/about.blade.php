{{-- ===================== ABOUT SECTION ===================== --}}
@php $a = $content['about'] ?? []; @endphp
<section id="about" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Text --}}
            <div>
                <span class="inline-block px-4 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full mb-4">
                    Who We Are
                </span>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6 leading-tight">
                    {{ $a['title'] ?? 'About Our Veterinary Platform' }}
                </h2>
                <div class="prose prose-lg text-gray-600 max-w-none leading-relaxed">
                    {!! nl2br(e($a['description'] ?? 'We are a leading veterinary pharmaceutical distribution platform connecting doctors, stores, and farmers across India.')) !!}
                </div>

                {{-- Quick stats --}}
                <div class="mt-10 grid grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-green-50 rounded-xl border border-green-100">
                        <div class="text-3xl font-bold text-green-700">2000+</div>
                        <div class="text-sm text-gray-500 mt-1">Doctors</div>
                    </div>
                    <div class="text-center p-4 bg-teal-50 rounded-xl border border-teal-100">
                        <div class="text-3xl font-bold text-teal-700">500+</div>
                        <div class="text-sm text-gray-500 mt-1">Stores</div>
                    </div>
                    <div class="text-center p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <div class="text-3xl font-bold text-emerald-700">5000+</div>
                        <div class="text-sm text-gray-500 mt-1">Orders/mo</div>
                    </div>
                </div>
            </div>

            {{-- Image --}}
            <div class="flex justify-center">
                @if(!empty($a['image']))
                    <img src="{{ asset('storage/' . $a['image']) }}" alt="About"
                         class="rounded-3xl shadow-2xl w-full max-w-lg object-cover border-4 border-green-100">
                @else
                    <div class="w-full max-w-lg h-80 bg-gradient-to-br from-green-100 to-teal-100 rounded-3xl shadow-2xl flex items-center justify-center border-4 border-green-100">
                        <div class="text-center">
                            <i class="fas fa-heartbeat text-green-400" style="font-size: 5rem;"></i>
                            <p class="text-green-500 mt-3 font-medium">Upload about image from CMS</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
