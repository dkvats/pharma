@extends('layouts.super-admin')
@section('title', 'Edit Section: ' . $section->section_title)
@section('page-subtitle', 'Edit content fields for this homepage section')

@section('content')
<div class="space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-slate-400">
        <a href="{{ route('super-admin.homepage-cms.index') }}" class="hover:text-white transition-colors">Homepage CMS</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-white">{{ $section->section_title }}</span>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/30 text-green-300 rounded-xl p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('super-admin.homepage-cms.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-slate-800 rounded-2xl border border-slate-700">
            {{-- Header --}}
            <div class="p-6 border-b border-slate-700 flex items-center justify-between">
                <div>
                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                        <i class="fas fa-edit text-green-400"></i>
                        Edit: {{ $section->section_title }}
                    </h3>
                    <p class="text-slate-400 text-sm mt-0.5">
                        Section key: <code class="text-green-400 bg-slate-700 px-1.5 py-0.5 rounded text-xs">{{ $section->section_key }}</code>
                        &middot; Sort order: <span class="text-white">{{ $section->sort_order }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('super-admin.homepage-cms.sections.toggle', $section) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors
                                    {{ $section->status === 'active'
                                        ? 'bg-orange-500/20 text-orange-400 hover:bg-orange-500/30'
                                        : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                            <i class="fas fa-{{ $section->status === 'active' ? 'eye-slash' : 'eye' }}"></i>
                            {{ $section->status === 'active' ? 'Hide Section' : 'Show Section' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Content Fields --}}
            <div class="p-6 space-y-6">

                @foreach($section->contents as $content)
                    @php
                        $key = $content->field_key;
                        $value = $content->field_value;
                        $isImage = str_contains($key, 'image') || str_contains($key, 'logo') || str_contains($key, 'photo');
                        $isLongText = str_contains($key, 'description') || str_contains($key, 'subtitle') || str_contains($key, 'desc');
                        $label = ucwords(str_replace('_', ' ', $key));
                    @endphp

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-300">
                            {{ $label }}
                            <code class="text-slate-500 text-xs font-mono ml-2">{{ $key }}</code>
                        </label>

                        @if($isImage)
                            {{-- Image field --}}
                            <div class="space-y-2">
                                @if($value)
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $value) }}" alt="{{ $label }}"
                                             class="h-20 w-32 object-cover rounded-lg border border-slate-600">
                                        <div class="text-xs text-slate-400">Current image</div>
                                    </div>
                                @endif
                                <input type="file" name="image_{{ $key }}" accept="image/*"
                                       class="block w-full text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                                <p class="text-slate-500 text-xs">Upload new image to replace current.</p>
                                {{-- Hidden to preserve existing value if no new upload --}}
                                <input type="hidden" name="fields[{{ $key }}]" value="{{ $value }}">
                            </div>
                        @elseif($isLongText)
                            {{-- Textarea --}}
                            <textarea name="fields[{{ $key }}]" rows="4"
                                      class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none">{{ $value }}</textarea>
                        @else
                            {{-- Regular input --}}
                            <input type="text" name="fields[{{ $key }}]" value="{{ $value }}"
                                   class="w-full bg-slate-700 border border-slate-600 text-white rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Enter {{ $label }}...">
                        @endif

                        @error('fields.' . $key)
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                @if($section->contents->isEmpty())
                    <div class="text-center py-8 text-slate-400">
                        <i class="fas fa-inbox text-3xl mb-2 block"></i>
                        No content fields defined for this section.
                    </div>
                @endif
            </div>

            {{-- Footer actions --}}
            <div class="p-6 border-t border-slate-700 flex items-center justify-between">
                <a href="{{ route('super-admin.homepage-cms.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white text-sm font-medium rounded-xl hover:bg-slate-500 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 text-white text-sm font-medium rounded-xl hover:bg-slate-500 transition-colors">
                        <i class="fas fa-external-link-alt"></i> Preview
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white text-sm font-bold rounded-xl hover:bg-green-700 transition-colors shadow-lg">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
