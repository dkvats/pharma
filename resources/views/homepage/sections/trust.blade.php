{{-- ===================== TRUST / WHY CHOOSE US SECTION ===================== --}}
@php $t = $content['trust'] ?? []; @endphp

<section id="trust" class="py-20 bg-gradient-to-br from-green-50 via-white to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left: Header + Trust Points --}}
            <div>
                <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 rounded-full px-4 py-1.5 text-sm font-semibold mb-4">
                    <i class="fas fa-award text-green-500"></i>
                    Why Choose Us
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                    {{ $t['title'] ?? 'Why Choose Us' }}
                </h2>
                <p class="text-lg text-gray-500 mb-10 leading-relaxed">
                    {{ $t['subtitle'] ?? 'We are committed to delivering quality veterinary medicines with reliability and trust.' }}
                </p>

                {{-- Trust Points --}}
                <div class="space-y-4">
                    @php
                        $trustPoints = [
                            ['icon' => 'flask',           'color' => 'green',  'key' => 'point_1'],
                            ['icon' => 'user-md',         'color' => 'teal',   'key' => 'point_2'],
                            ['icon' => 'truck',           'color' => 'blue',   'key' => 'point_3'],
                            ['icon' => 'microscope',      'color' => 'purple', 'key' => 'point_4'],
                        ];
                        $colorMap = [
                            'green'  => ['bg' => 'bg-green-100',  'text' => 'text-green-600',  'border' => 'border-green-500'],
                            'teal'   => ['bg' => 'bg-teal-100',   'text' => 'text-teal-600',   'border' => 'border-teal-500'],
                            'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'border' => 'border-blue-500'],
                            'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-500'],
                        ];
                    @endphp

                    @foreach($trustPoints as $point)
                        @php
                            $title = $t[$point['key'] . '_title'] ?? '';
                            $desc  = $t[$point['key'] . '_desc'] ?? '';
                            $c = $colorMap[$point['color']];
                        @endphp
                        @if($title)
                            <div class="trust-card rounded-xl p-5 {{ $c['border'] }} flex items-start gap-4 hover:shadow-lg transition-all">
                                <div class="w-12 h-12 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-{{ $point['icon'] }} {{ $c['text'] }} text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">{{ $title }}</h4>
                                    <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Stats & Certifications visual --}}
            <div class="relative">
                <div class="absolute -inset-4 bg-green-100 rounded-3xl opacity-60 blur-2xl"></div>
                <div class="relative bg-white rounded-3xl shadow-xl border border-green-100 p-8">

                    {{-- Main stat --}}
                    <div class="text-center mb-8">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
                            <i class="fas fa-heartbeat text-white text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900">Trusted Healthcare Partner</h3>
                        <p class="text-gray-500 text-sm mt-1">For Indian Farmers & Veterinary Professionals</p>
                    </div>

                    {{-- Mini stats grid --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-green-50 rounded-2xl p-4 text-center border border-green-100">
                            <div class="text-2xl font-black text-green-600 mb-0.5">15+</div>
                            <div class="text-xs text-gray-500 font-medium">Years Experience</div>
                        </div>
                        <div class="bg-teal-50 rounded-2xl p-4 text-center border border-teal-100">
                            <div class="text-2xl font-black text-teal-600 mb-0.5">500+</div>
                            <div class="text-xs text-gray-500 font-medium">Product Variants</div>
                        </div>
                        <div class="bg-blue-50 rounded-2xl p-4 text-center border border-blue-100">
                            <div class="text-2xl font-black text-blue-600 mb-0.5">2000+</div>
                            <div class="text-xs text-gray-500 font-medium">Registered Doctors</div>
                        </div>
                        <div class="bg-purple-50 rounded-2xl p-4 text-center border border-purple-100">
                            <div class="text-2xl font-black text-purple-600 mb-0.5">28</div>
                            <div class="text-xs text-gray-500 font-medium">States Covered</div>
                        </div>
                    </div>

                    {{-- Certification badges --}}
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-xs text-gray-400 font-semibold text-center mb-3 uppercase tracking-wider">Quality Certifications</p>
                        <div class="flex justify-center gap-3 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                <i class="fas fa-certificate"></i> GMP Certified
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                <i class="fas fa-shield-alt"></i> ISO 9001
                            </span>
                            <span class="inline-flex items-center gap-1.5 bg-orange-100 text-orange-700 text-xs font-bold px-3 py-1.5 rounded-full">
                                <i class="fas fa-award"></i> FSSAI Approved
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
