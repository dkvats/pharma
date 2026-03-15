@extends('layouts.master')

@section('title', 'Edit Feature – Platform Features')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.homepage-features.index') }}" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-star mr-1"></i>Platform Features
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $feature->title }}</span>
    </nav>

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i>
                Edit Feature
            </h1>
            <p class="text-sm text-gray-500 mt-1">Modify the feature card details.</p>
        </div>
        <form method="POST" action="{{ route('admin.homepage-features.toggle', $feature) }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                    {{ $feature->status === 'active'
                        ? 'bg-red-100 text-red-700 hover:bg-red-200'
                        : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                <i class="fas {{ $feature->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                {{ $feature->status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.homepage-features.update', $feature) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Feature Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $feature->title) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., Doctor Rewards" required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Brief description of this feature...">{{ old('description', $feature->description) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Max 500 characters.</p>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Input (Free Text) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Icon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="icon" value="{{ old('icon', $feature->icon) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="e.g., user-md, chart-line, boxes" required>
                    <p class="text-xs text-gray-400 mt-1">Font Awesome icon name (without fa- prefix). <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-500 hover:underline">Browse icons</a></p>
                    @error('icon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    {{-- Icon preview --}}
                    <div class="mt-2 flex items-center gap-2 text-gray-500 text-sm">
                        <span>Preview:</span>
                        <i class="fas fa-{{ old('icon', $feature->icon) }} text-lg text-blue-600" id="icon-preview"></i>
                    </div>
                </div>

                {{-- Icon Color --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Icon Color <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $colors = [
                                'blue'   => ['bg-blue-100', 'text-blue-600'],
                                'yellow' => ['bg-yellow-100', 'text-yellow-600'],
                                'green'  => ['bg-green-100', 'text-green-600'],
                                'purple' => ['bg-purple-100', 'text-purple-600'],
                                'red'    => ['bg-red-100', 'text-red-600'],
                                'indigo' => ['bg-indigo-100', 'text-indigo-600'],
                                'orange' => ['bg-orange-100', 'text-orange-600'],
                                'pink'   => ['bg-pink-100', 'text-pink-600'],
                                'cyan'   => ['bg-cyan-100', 'text-cyan-600'],
                            ];
                        @endphp
                        @foreach($colors as $colorName => $classes)
                            <label class="cursor-pointer">
                                <input type="radio" name="icon_color" value="{{ $colorName }}" class="hidden peer"
                                       {{ old('icon_color', $feature->icon_color) === $colorName ? 'checked' : '' }}>
                                <div class="w-10 h-10 rounded-xl {{ $classes[0] }} peer-checked:ring-2 peer-checked:ring-blue-500 flex items-center justify-center hover:opacity-80 transition-all">
                                    <i class="fas fa-star {{ $classes[1] }}"></i>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('icon_color')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="active" class="text-blue-600"
                                   {{ old('status', $feature->status) === 'active' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Active (visible)</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="status" value="inactive" class="text-blue-600"
                                   {{ old('status', $feature->status) === 'inactive' ? 'checked' : '' }}>
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
                <a href="{{ route('admin.homepage-features.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Back to List
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
