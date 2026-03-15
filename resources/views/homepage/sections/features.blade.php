{{-- ===================== FEATURES SECTION ===================== --}}
@php $f = $content['features'] ?? []; @endphp
<section id="features" class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Heading --}}
        <div class="text-center mb-16">
            <span class="inline-block px-4 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full mb-4">
                Platform Features
            </span>
            <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                {{ $f['title'] ?? 'Everything You Need' }}
            </h2>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto">
                {{ $f['subtitle'] ?? 'A fully integrated system designed for the modern pharmaceutical industry.' }}
            </p>
        </div>

        {{-- Feature Cards Grid (Dynamic from Database) --}}
        @if($features->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($features as $feature)
                    <div class="feature-card bg-white rounded-2xl shadow-md p-8 border border-gray-100">
                        <div class="w-14 h-14 {{ $feature->icon_bg_class }} rounded-xl flex items-center justify-center mb-5">
                            <i class="{{ $feature->icon_class }} {{ $feature->icon_text_class }} text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $feature->title }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ $feature->description }}</p>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Fallback if no features configured --}}
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-star text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg">No features configured yet.</p>
            </div>
        @endif
    </div>
</section>
