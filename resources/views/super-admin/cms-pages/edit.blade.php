@extends('layouts.super-admin')

@section('title', 'Edit CMS Page')
@section('page-title', 'Edit CMS Page')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('super-admin.cms-pages.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Pages
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Edit: {{ $page->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">Last updated: {{ $page->updated_at->format('M d, Y H:i') }}</p>
        </div>
        <form method="POST" action="{{ route('super-admin.cms-pages.update', $page) }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Page Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 text-sm mr-2">/page/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500" required>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Page Content</label>
                    <textarea name="content" id="content" rows="15" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 font-mono text-sm" required>{{ old('content', $page->content) }}</textarea>
                </div>

                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description (SEO)</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ $page->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $page->status === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('super-admin.cms-pages.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Update Page</button>
            </div>
        </form>
    </div>
</div>
@endsection
