{{-- ===================== FOOTER SECTION ===================== --}}
@php $f = $content['footer'] ?? []; @endphp
<footer id="footer" class="bg-gray-950 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand & Description --}}
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    @if($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" alt="{{ $settings->site_name }}" class="h-8 w-auto object-contain brightness-200">
                    @else
                        <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-heartbeat text-white text-lg"></i>
                        </div>
                    @endif
                    <div>
                        <span class="font-bold text-lg text-white block">{{ $settings->site_name }}</span>
                        @if($settings->tagline)
                            <span class="text-green-400 text-xs">{{ $settings->tagline }}</span>
                        @endif
                    </div>
                </div>
                @if(!empty($f['description']))
                    <p class="text-gray-400 text-sm leading-relaxed max-w-md mb-5">
                        {{ $f['description'] }}
                    </p>
                @endif

                {{-- Social Links - from footer CMS content OR site_settings --}}
                <div class="flex items-center gap-3">
                    @php
                        $fb = $f['facebook'] ?? $settings->facebook_url ?? null;
                        $tw = $f['twitter'] ?? $settings->twitter_url ?? null;
                        $li = $f['linkedin'] ?? $settings->linkedin_url ?? null;
                        $ig = $settings->instagram_url ?? null;
                        $wa = $settings->whatsapp_number ?? null;
                    @endphp
                    @if($fb)
                        <a href="{{ $fb }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-blue-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                    @endif
                    @if($tw)
                        <a href="{{ $tw }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-sky-500 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                    @endif
                    @if($li)
                        <a href="{{ $li }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-blue-700 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                    @endif
                    @if($ig)
                        <a href="{{ $ig }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-sm"></i>
                        </a>
                    @endif
                    @if($wa)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank" rel="noopener"
                           class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Contact Info --}}
            <div>
                <h4 class="font-semibold text-white mb-5 text-sm uppercase tracking-wider">Contact Us</h4>
                <ul class="space-y-3 text-sm text-gray-400">
                    @php
                        $phone   = $f['phone']   ?? $settings->contact_phone ?? null;
                        $email   = $f['email']   ?? $settings->contact_email ?? null;
                        $address = $f['address'] ?? $settings->address ?? null;
                    @endphp
                    @if($phone)
                        <li class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-green-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-green-300 text-xs"></i>
                            </div>
                            <a href="tel:{{ $phone }}" class="hover:text-white transition-colors">{{ $phone }}</a>
                        </li>
                    @endif
                    @if($email)
                        <li class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-green-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-green-300 text-xs"></i>
                            </div>
                            <a href="mailto:{{ $email }}" class="hover:text-white transition-colors">{{ $email }}</a>
                        </li>
                    @endif
                    @if($address)
                        <li class="flex items-start gap-3">
                            <div class="w-7 h-7 bg-green-800 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-map-marker-alt text-green-300 text-xs"></i>
                            </div>
                            <span class="leading-relaxed">{{ $address }}</span>
                        </li>
                    @endif
                    @if(!$phone && !$email && !$address)
                        <li class="text-gray-600 text-xs italic">Contact details can be added from Super Admin CMS.</li>
                    @endif
                </ul>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="font-semibold text-white mb-5 text-sm uppercase tracking-wider">Quick Links</h4>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="#hero" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-chevron-right text-xs text-green-600"></i>Home
                        </a>
                    </li>
                    <li>
                        <a href="#products" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-chevron-right text-xs text-green-600"></i>Products
                        </a>
                    </li>
                    <li>
                        <a href="#animals" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-chevron-right text-xs text-green-600"></i>Animals
                        </a>
                    </li>
                    <li>
                        <a href="#about" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-chevron-right text-xs text-green-600"></i>About Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('login') }}" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-sign-in-alt text-xs text-green-600"></i>Login
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="text-gray-400 hover:text-green-400 transition-colors flex items-center gap-2">
                            <i class="fas fa-user-plus text-xs text-green-600"></i>Register
                        </a>
                    </li>
                </ul>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="border-t border-gray-800 mt-12 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ $settings->site_name }}. {{ $f['copyright'] ?? 'All rights reserved.' }}
            </p>
            <div class="flex items-center gap-4 text-xs text-gray-600">
                <span class="flex items-center gap-1.5">
                    <i class="fas fa-shield-alt text-green-600"></i> Quality Certified
                </span>
                <span class="flex items-center gap-1.5">
                    <i class="fas fa-leaf text-green-600"></i> Veterinary Pharma
                </span>
            </div>
        </div>
    </div>
</footer>
