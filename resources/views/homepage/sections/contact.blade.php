{{-- ===================== CONTACT SECTION ===================== --}}
@php $c = $content['contact'] ?? []; @endphp
<section id="contact" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full mb-4">
                Get In Touch
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Contact Us</h2>
            <p class="text-xl text-gray-500">Reach out to our team for any inquiries or support.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            @if(!empty($c['phone']))
            <div class="text-center p-8 bg-blue-50 rounded-2xl border border-blue-100">
                <div class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone-alt text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Phone</h3>
                <a href="tel:{{ $c['phone'] }}" class="text-blue-600 hover:underline font-medium">{{ $c['phone'] }}</a>
            </div>
            @endif

            @if(!empty($c['email']))
            <div class="text-center p-8 bg-green-50 rounded-2xl border border-green-100">
                <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                <a href="mailto:{{ $c['email'] }}" class="text-green-600 hover:underline font-medium">{{ $c['email'] }}</a>
            </div>
            @endif

            @if(!empty($c['address']))
            <div class="text-center p-8 bg-purple-50 rounded-2xl border border-purple-100">
                <div class="w-14 h-14 bg-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Address</h3>
                <p class="text-gray-600 font-medium">{{ $c['address'] }}</p>
            </div>
            @endif

            @if(!empty($c['map_link']))
            <div class="text-center p-8 bg-orange-50 rounded-2xl border border-orange-100">
                <div class="w-14 h-14 bg-orange-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map text-white text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Find Us</h3>
                <a href="{{ $c['map_link'] }}" target="_blank" class="text-orange-600 hover:underline font-medium">
                    View on Maps <i class="fas fa-external-link-alt ml-1"></i>
                </a>
            </div>
            @endif

            @if(empty($c['phone']) && empty($c['email']) && empty($c['address']) && empty($c['map_link']))
            <div class="col-span-full text-center py-16 text-gray-400">
                <i class="fas fa-phone-alt text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">Contact details not configured yet.<br>
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Admin can add them in the Homepage Manager.</a>
                </p>
            </div>
            @endif

        </div>
    </div>
</section>
