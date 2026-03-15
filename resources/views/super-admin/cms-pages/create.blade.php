@extends('layouts.super-admin')

@section('title', 'Create CMS Page')
@section('page-title', 'Create CMS Page')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.cms-pages.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pages
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">New CMS Page</h3>
            <p class="text-sm text-gray-500 mt-1">Create a new content page for your website</p>
        </div>
        <form method="POST" action="{{ route('super-admin.cms-pages.store') }}">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 text-sm mr-2">/page/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Use lowercase letters, numbers, and hyphens only (e.g., about-us)</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Page Content</label>
                    <textarea name="content" id="content" rows="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm" required>{{ old('content') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Supports HTML content</p>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description') }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('super-admin.cms-pages.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Create Page</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
@endpush
@endsection
