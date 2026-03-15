@extends('layouts.super-admin')
@section('title', 'Homepage CMS')
@section('page-subtitle', 'Veterinary Pharma Landing Page Control')

@section('content')
<div class="space-y-6">

    {{-- Header Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-lg"></i>
                </div>
                <span class="text-green-200 text-sm font-medium">Total</span>
            </div>
            <div class="text-3xl font-black mb-0.5">{{ $sections->count() }}</div>
            <div class="text-green-200 text-sm">Homepage Sections</div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-toggle-on text-lg"></i>
                </div>
                <span class="text-emerald-200 text-sm font-medium">Active</span>
            </div>
            <div class="text-3xl font-black mb-0.5">{{ $sections->where('status', 'active')->count() }}</div>
            <div class="text-emerald-200 text-sm">Active Sections</div>
        </div>

        <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-lg"></i>
                </div>
                <span class="text-teal-200 text-sm font-medium">Featured</span>
            </div>
            <div class="text-3xl font-black mb-0.5">{{ $featuredProductsCount }}</div>
            <div class="text-teal-200 text-sm">Featured Products</div>
        </div>

        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-pills text-lg"></i>
                </div>
                <span class="text-cyan-200 text-sm font-medium">Total</span>
            </div>
            <div class="text-3xl font-black mb-0.5">{{ $totalProducts }}</div>
            <div class="text-cyan-200 text-sm">Active Products</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
        <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-bolt text-yellow-400"></i> Quick Actions
        </h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('super-admin.homepage-cms.site-settings') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors">
                <i class="fas fa-cog"></i> Site Settings
            </a>
            <a href="{{ route('super-admin.homepage-cms.featured-products') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-xl hover:bg-teal-700 transition-colors">
                <i class="fas fa-star"></i> Featured Products
            </a>
            <a href="{{ url('/') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-600 text-white text-sm font-semibold rounded-xl hover:bg-slate-500 transition-colors">
                <i class="fas fa-external-link-alt"></i> Preview Homepage
            </a>
        </div>
    </div>

    {{-- Sections Manager --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700">
        <div class="p-6 border-b border-slate-700 flex items-center justify-between">
            <div>
                <h3 class="text-white font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-layer-group text-green-400"></i> Homepage Sections
                </h3>
                <p class="text-slate-400 text-sm mt-0.5">Click "Edit" to change content. Toggle to show/hide sections.</p>
            </div>
        </div>

        <div class="divide-y divide-slate-700">
            @forelse($sections->sortBy('sort_order') as $section)
                <div class="p-5 flex items-center justify-between gap-4 hover:bg-slate-750 transition-colors">
                    <div class="flex items-center gap-4 min-w-0">
                        {{-- Sort order badge --}}
                        <div class="w-8 h-8 bg-slate-700 rounded-lg flex items-center justify-center text-slate-400 text-sm font-bold flex-shrink-0">
                            {{ $section->sort_order }}
                        </div>

                        {{-- Section info --}}
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-white font-semibold text-sm">{{ $section->section_title }}</span>
                                <code class="text-slate-500 text-xs bg-slate-700 px-1.5 py-0.5 rounded font-mono">{{ $section->section_key }}</code>
                            </div>
                            <div class="text-slate-400 text-xs">
                                {{ $section->contents->count() }} content field(s)
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Status badge --}}
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full
                            {{ $section->status === 'active' ? 'bg-green-500/20 text-green-400' : 'bg-slate-600 text-slate-400' }}">
                            <i class="fas fa-circle text-[6px]"></i>
                            {{ ucfirst($section->status) }}
                        </span>

                        {{-- Edit button --}}
                        <a href="{{ route('super-admin.homepage-cms.sections.edit', $section) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        {{-- Toggle button --}}
                        <form action="{{ route('super-admin.homepage-cms.sections.toggle', $section) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                        {{ $section->status === 'active'
                                            ? 'bg-orange-500/20 text-orange-400 hover:bg-orange-500/30'
                                            : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                                <i class="fas fa-{{ $section->status === 'active' ? 'eye-slash' : 'eye' }}"></i>
                                {{ $section->status === 'active' ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-layer-group text-4xl mb-3 block text-slate-600"></i>
                    No sections found.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Site Info Preview --}}
    <div class="bg-slate-800 rounded-2xl border border-slate-700 p-6">
        <h3 class="text-white font-bold text-lg flex items-center gap-2 mb-4">
            <i class="fas fa-globe text-green-400"></i> Current Site Settings
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Site Name</div>
                <div class="text-white font-medium">{{ $settings->site_name ?? 'Not set' }}</div>
            </div>
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Tagline</div>
                <div class="text-white font-medium">{{ $settings->tagline ?? 'Not set' }}</div>
            </div>
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Contact Phone</div>
                <div class="text-white font-medium">{{ $settings->contact_phone ?? 'Not set' }}</div>
            </div>
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Contact Email</div>
                <div class="text-white font-medium">{{ $settings->contact_email ?? 'Not set' }}</div>
            </div>
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Logo</div>
                <div class="text-white font-medium">
                    @if($settings->logo_url)
                        <img src="{{ $settings->logo_url }}" class="h-8 object-contain" alt="Logo">
                    @else
                        <span class="text-slate-400 italic">No logo uploaded</span>
                    @endif
                </div>
            </div>
            <div class="bg-slate-700/50 rounded-xl p-4">
                <div class="text-slate-400 text-xs mb-1 uppercase tracking-wider">Social Links</div>
                <div class="text-white font-medium flex gap-2">
                    @if($settings->facebook_url) <i class="fab fa-facebook text-blue-400"></i> @endif
                    @if($settings->twitter_url) <i class="fab fa-twitter text-sky-400"></i> @endif
                    @if($settings->instagram_url) <i class="fab fa-instagram text-pink-400"></i> @endif
                    @if($settings->linkedin_url) <i class="fab fa-linkedin text-blue-500"></i> @endif
                    @if($settings->whatsapp_number) <i class="fab fa-whatsapp text-green-400"></i> @endif
                    @if(!$settings->facebook_url && !$settings->twitter_url && !$settings->instagram_url && !$settings->linkedin_url && !$settings->whatsapp_number)
                        <span class="text-slate-400 italic text-xs">No social links set</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('super-admin.homepage-cms.site-settings') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-colors">
                <i class="fas fa-edit"></i> Edit Site Settings
            </a>
        </div>
    </div>

</div>
@endsection
