@extends('layouts.master')

@section('title', 'Edit Section: ' . $section->section_title)

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.homepage-manager.index') }}" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-layer-group mr-1"></i>Homepage Manager
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $section->section_title }}</span>
    </nav>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i>
                Edit: {{ $section->section_title }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Key: <code class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded font-mono">{{ $section->section_key }}</code>
                &nbsp;|&nbsp;
                Status:
                <span class="font-semibold {{ $section->status === 'active' ? 'text-green-600' : 'text-red-500' }}">
                    {{ ucfirst($section->status) }}
                </span>
            </p>
        </div>
        <div class="flex gap-3">
            {{-- Toggle status --}}
            <form method="POST" action="{{ route('admin.homepage-manager.toggle', $section) }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                        {{ $section->status === 'active'
                            ? 'bg-red-100 text-red-700 hover:bg-red-200'
                            : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                    <i class="fas {{ $section->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                    {{ $section->status === 'active' ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            {{-- Preview --}}
            <a href="{{ url('/') }}#{{ $section->section_key }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-colors">
                <i class="fas fa-external-link-alt"></i> Preview
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}
        </div>
    @endif

    {{-- CONTENT EDIT FORM --}}
    <form method="POST" action="{{ route('admin.homepage-manager.update', $section) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-bold text-gray-800">Section Content Fields</h2>
                <p class="text-xs text-gray-400 mt-0.5">All fields are optional. Leave blank to use defaults.</p>
            </div>
            <div class="p-6 space-y-6">

                {{-- ===== HERO ===== --}}
                @if($section->section_key === 'hero')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Main Title</label>
                        <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Connecting Doctors, Stores & Patients">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle / Tagline</label>
                        <textarea name="subtitle" rows="3"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="A professional pharmaceutical distribution platform...">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="button_text" value="{{ old('button_text', $content['button_text'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Login to Platform">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                            <input type="text" name="button_link" value="{{ old('button_link', $content['button_link'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="/login">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hero Banner Image</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(!empty($content['image']))
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $content['image']) }}" alt="Hero Image"
                                     class="rounded-xl shadow max-h-48 object-cover">
                                <p class="text-xs text-gray-400 mt-1">Current hero image. Upload new to replace.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ===== ABOUT ===== --}}
                @if($section->section_key === 'about')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="About Our Platform">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="6"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Describe your platform here...">{{ old('description', $content['description'] ?? '') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Supports line breaks.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">About Image</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(!empty($content['image']))
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $content['image']) }}" alt="About Image"
                                     class="rounded-xl shadow max-h-48 object-cover">
                                <p class="text-xs text-gray-400 mt-1">Current about image. Upload new to replace.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ===== FEATURES ===== --}}
                @if($section->section_key === 'features')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Everything You Need">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle', $content['subtitle'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="A fully integrated system...">
                    </div>
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Feature cards (Doctor Rewards, Spin & Win, etc.) are built-in. Edit the title and subtitle above to customize this section.
                    </div>
                @endif

                {{-- ===== CTA ===== --}}
                @if($section->section_key === 'cta')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Title</label>
                        <input type="text" name="title" value="{{ old('title', $content['title'] ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ready to Get Started?">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Subtitle</label>
                        <textarea name="subtitle" rows="3"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Login to your account or register to join the platform.">{{ old('subtitle', $content['subtitle'] ?? '') }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="button_text" value="{{ old('button_text', $content['button_text'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Login Now">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                            <input type="text" name="button_link" value="{{ old('button_link', $content['button_link'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="/login">
                        </div>
                    </div>
                @endif

                {{-- ===== CONTACT ===== --}}
                @if($section->section_key === 'contact')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone-alt mr-1 text-blue-500"></i>Phone Number
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $content['phone'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="+91 98765 43210">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope mr-1 text-green-500"></i>Email Address
                            </label>
                            <input type="email" name="email" value="{{ old('email', $content['email'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="info@pharma.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-map-marker-alt mr-1 text-purple-500"></i>Address
                            </label>
                            <textarea name="address" rows="3"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="123 Pharma Street, Mumbai, India">{{ old('address', $content['address'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-map mr-1 text-orange-500"></i>Google Maps Link
                            </label>
                            <input type="url" name="map_link" value="{{ old('map_link', $content['map_link'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="https://maps.google.com/...">
                        </div>
                    </div>
                @endif

                {{-- ===== FALLBACK FOR UNKNOWN SECTIONS ===== --}}
                @if(!in_array($section->section_key, ['hero','about','features','cta','contact']))
                    @if($section->contents->count() > 0)
                        @foreach($section->contents as $field)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ ucwords(str_replace('_', ' ', $field->field_key)) }}
                                <code class="ml-2 text-xs text-gray-400 font-mono">{{ $field->field_key }}</code>
                            </label>
                            <textarea name="{{ $field->field_key }}" rows="3"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old($field->field_key, $field->field_value ?? '') }}</textarea>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-10 text-gray-400">
                            <i class="fas fa-info-circle text-3xl mb-3 block opacity-30"></i>
                            <p>No editable fields defined for this section yet.</p>
                        </div>
                    @endif
                @endif

            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.homepage-manager.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to Manager
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
