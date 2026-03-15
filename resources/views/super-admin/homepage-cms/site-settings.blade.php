@extends('layouts.super-admin')
@section('title', 'Site Settings')
@section('page-subtitle', 'Logo, Contact, Social Links & Hero Image')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('super-admin.homepage-cms.index') }}" class="hover:text-white transition-colors">Homepage CMS</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-white">Site Settings</span>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-300 rounded-xl p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('super-admin.homepage-cms.site-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Branding --}}
            <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 space-y-5">
                <h3 class="text-white font-bold text-base flex items-center gap-2 pb-3 border-b border-slate-700">
                    <i class="fas fa-palette text-green-400"></i> Branding
                </h3>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Site Name <span class="text-red-400">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                           placeholder="e.g. VetPharma India">
                    @error('site_name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $settings->tagline) }}"
                           class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                           placeholder="e.g. Quality Veterinary Medicines">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Logo</label>
                    @if($settings->logo_url)
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ $settings->logo_url }}" alt="Logo" class="h-12 object-contain bg-white rounded-lg p-1">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*"
                           class="block w-full text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                    <p class="text-slate-500 text-xs mt-1">Recommended: 200x60px PNG with transparent background</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Hero Banner Image</label>
                    @if($settings->hero_image)
                        <div class="mb-2 rounded-xl overflow-hidden border border-slate-600">
                            <img src="{{ $settings->hero_image_url }}" alt="Hero" class="w-full h-32 object-cover">
                        </div>
                    @endif
                    <input type="file" name="hero_image" accept="image/*"
                           class="block w-full text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                    <p class="text-slate-500 text-xs mt-1">Recommended: 1200x600px. Max 4MB. Also updates Hero section image.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-1.5">Favicon</label>
                    <input type="file" name="favicon" accept="image/*"
                           class="block w-full text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                    <p class="text-slate-500 text-xs mt-1">32x32px ICO/PNG</p>
                </div>
            </div>

            {{-- Contact + Social --}}
            <div class="space-y-6">
                <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 space-y-4">
                    <h3 class="text-white font-bold text-base flex items-center gap-2 pb-3 border-b border-slate-700">
                        <i class="fas fa-address-card text-green-400"></i> Contact Information
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Phone Number</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings->contact_phone) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="+91 98765 43210">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Email Address</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $settings->contact_email) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="info@vetpharma.in">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5">Address</label>
                        <textarea name="address" rows="3"
                                  class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm resize-none"
                                  placeholder="123 Main Road, City - 400001">{{ old('address', $settings->address) }}</textarea>
                    </div>
                </div>

                <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6 space-y-4">
                    <h3 class="text-white font-bold text-base flex items-center gap-2 pb-3 border-b border-slate-700">
                        <i class="fas fa-share-alt text-green-400"></i> Social Media Links
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5"><i class="fab fa-facebook text-blue-400 mr-1"></i> Facebook URL</label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="https://facebook.com/yourpage">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5"><i class="fab fa-twitter text-sky-400 mr-1"></i> Twitter URL</label>
                        <input type="url" name="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="https://twitter.com/yourhandle">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5"><i class="fab fa-instagram text-pink-400 mr-1"></i> Instagram URL</label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="https://instagram.com/yourpage">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5"><i class="fab fa-linkedin text-blue-500 mr-1"></i> LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $settings->linkedin_url) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="https://linkedin.com/company/yourcompany">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1.5"><i class="fab fa-whatsapp text-green-400 mr-1"></i> WhatsApp Number</label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->whatsapp_number) }}"
                               class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm"
                               placeholder="919876543210 (with country code)">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('super-admin.homepage-cms.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-xl hover:bg-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    <i class="fas fa-external-link-alt"></i> Preview
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-colors shadow-lg">
                    <i class="fas fa-save"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection