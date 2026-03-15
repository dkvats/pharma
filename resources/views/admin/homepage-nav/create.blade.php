@extends('layouts.master')

@section('title', 'Add Navigation Link')

@section('content')
<div class="p-6 max-w-2xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.homepage-nav.index') }}" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-bars mr-1"></i>Navigation Menu
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Add Link</span>
    </nav>

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-plus-circle text-blue-600"></i>
            Add Navigation Link
        </h1>
        <p class="text-sm text-gray-500 mt-1">Create a new link for the homepage navbar.</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.homepage-nav.store') }}">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">

                {{-- Label --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Link Label <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="label" value="{{ old('label') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Home, About, Contact" required>
                    @error('label')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- URL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        URL <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="url" value="{{ old('url') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., #about, /login, https://example.com" required>
                    <p class="text-xs text-gray-400 mt-1">Use # for anchors (e.g., #about), / for internal links, or full URL for external.</p>
                    @error('url')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Is External --}}
                <div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_external" value="1" {{ old('is_external') ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-gray-700">Open in new tab</span>
                            <p class="text-xs text-gray-400">Mark as external link (opens in new window).</p>
                        </div>
                    </label>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="active" class="text-blue-600"
                                   {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Active (visible)</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="inactive" class="text-blue-600"
                                   {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Inactive (hidden)</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.homepage-nav.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                    <i class="fas fa-plus"></i> Create Link
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
