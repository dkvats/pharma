@extends('layouts.master')

@section('title', 'Add Slide – Homepage Slider')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('admin.homepage-slides.index') }}" class="hover:text-green-600 transition-colors">
            <i class="fas fa-images mr-1"></i>Homepage Slides
        </a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Add Slide</span>
    </nav>

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-plus-circle text-green-600"></i> Add New Slide
        </h1>
        <p class="text-sm text-gray-500 mt-1">Upload an image and fill in the optional text to create a homepage slider slide.</p>
    </div>

    {{-- Validation errors summary --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4">
            <p class="text-red-700 font-semibold text-sm mb-2"><i class="fas fa-exclamation-circle mr-1"></i>Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-red-600 text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.homepage-slides.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 space-y-6">

                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Slide Image <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-400 transition-colors" id="drop-zone">
                        <input type="file" name="image" id="image-input" accept="image/*"
                               class="hidden" onchange="previewImage(this)">
                        <div id="image-preview-wrap" class="hidden mb-4">
                            <img id="image-preview" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg shadow border border-gray-200">
                        </div>
                        <label for="image-input" class="cursor-pointer">
                            <div id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3 block"></i>
                                <p class="text-sm font-medium text-gray-600">Click to upload image</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP — max 4 MB</p>
                                <p class="text-xs text-gray-400">Recommended: 1920 × 600 px (WebP for best performance)</p>
                            </div>
                            <span id="upload-change-btn" class="hidden inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-sync-alt"></i> Change image
                            </span>
                        </label>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('title') border-red-400 @enderror"
                           placeholder="e.g., Premium Veterinary Medicines">
                    <p class="text-xs text-gray-400 mt-1">Optional. Displayed as the main headline on the slide.</p>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Subtitle --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                    <textarea name="subtitle" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('subtitle') border-red-400 @enderror"
                              placeholder="e.g., Trusted solutions for cattle, buffalo, and poultry across India.">{{ old('subtitle') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Optional. Displayed below the title.</p>
                    @error('subtitle')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Button Text & Link --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" name="button_text" value="{{ old('button_text') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="e.g., Explore Products">
                        <p class="text-xs text-gray-400 mt-1">Optional. Leave blank to hide button.</p>
                        @error('button_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                        <input type="text" name="button_link" value="{{ old('button_link') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="e.g., #products or /login">
                        <p class="text-xs text-gray-400 mt-1">URL or anchor (#products).</p>
                        @error('button_link')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="active" class="text-green-600"
                                   {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Active <span class="text-green-600 text-xs">(visible to visitors)</span></span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="inactive" class="text-green-600"
                                   {{ old('status') === 'inactive' ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Inactive <span class="text-gray-400 text-xs">(hidden)</span></span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.homepage-slides.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                    <i class="fas fa-plus"></i> Create Slide
                </button>
            </div>
        </div>

    </form>
</div>

<script>
function previewImage(input) {
    var wrap = document.getElementById('image-preview-wrap');
    var img  = document.getElementById('image-preview');
    var placeholder = document.getElementById('upload-placeholder');
    var changeBtn   = document.getElementById('upload-change-btn');

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
            placeholder.classList.add('hidden');
            changeBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
