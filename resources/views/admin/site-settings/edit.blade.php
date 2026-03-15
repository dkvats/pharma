@extends('layouts.app')

@section('title', 'Website Settings')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-4xl">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Website Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Customize the public homepage visible to visitors.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <i class="fas fa-external-link-alt"></i> Preview Homepage
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg px-4 py-3 mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- ===== BRANDING ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-palette text-blue-500"></i> Branding
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Site Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Website Name <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('site_name') border-red-400 @enderror"
                        placeholder="e.g. Pharma Distribution Network">
                    @error('site_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Logo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Image</label>
                    @if($settings->logo_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $settings->logo_url }}" alt="Current Logo" class="h-12 w-auto object-contain border border-gray-200 rounded-lg p-1">
                            <span class="text-xs text-gray-400">Current logo</span>
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 @error('logo') border-red-400 @enderror">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, SVG, WebP. Max 2MB. Leave empty to keep current.</p>
                    @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===== HERO SECTION ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-image text-purple-500"></i> Hero / Banner Section
            </h2>
            <div class="space-y-5">
                <!-- Hero Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Title <span class="text-red-500">*</span></label>
                    <input type="text" name="hero_title" value="{{ old('hero_title', $settings->hero_title) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hero_title') border-red-400 @enderror"
                        placeholder="e.g. Connecting Doctors, Stores & Patients">
                    @error('hero_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hero Subtitle -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hero_subtitle') border-red-400 @enderror"
                        placeholder="A short description shown below the hero title...">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                    @error('hero_subtitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hero Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hero Banner Image</label>
                    @if($settings->hero_image_url)
                        <div class="mb-2">
                            <img src="{{ $settings->hero_image_url }}" alt="Current Banner" class="h-32 w-auto object-cover rounded-lg border border-gray-200">
                            <p class="text-xs text-gray-400 mt-1">Current banner</p>
                        </div>
                    @endif
                    <input type="file" name="hero_image" accept="image/*"
                        class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:font-medium hover:file:bg-purple-100 @error('hero_image') border-red-400 @enderror">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG, WebP. Max 4MB. Leave empty to keep current or use default illustration.</p>
                    @error('hero_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===== ABOUT SECTION ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-info-circle text-green-500"></i> About Section
            </h2>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">About Section Title</label>
                    <input type="text" name="about_title" value="{{ old('about_title', $settings->about_title) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. About Us">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">About Description</label>
                    <textarea name="about_description" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('about_description') border-red-400 @enderror"
                        placeholder="Describe your platform...">{{ old('about_description', $settings->about_description) }}</textarea>
                    @error('about_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ===== CONTACT SECTION ===== --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                <i class="fas fa-phone text-orange-500"></i> Contact Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+91 98765 43210">
                    @error('contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="contact@pharma.com">
                    @error('contact_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Full office address...">{{ old('address', $settings->address) }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('home') }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <i class="fas fa-eye"></i> Preview public homepage
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
