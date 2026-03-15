@extends('layouts.super-admin')

@section('title', 'CMS Pages')
@section('page-title', 'CMS Pages')
@section('page-subtitle', 'Manage dynamic content pages')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Manage Content Pages</h3>
            <p class="text-sm text-gray-500">Create and edit CMS pages like About, Terms, Privacy Policy, etc.</p>
        </div>
        <a href="{{ route('super-admin.cms-pages.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>
            Create Page
        </a>
    </div>

    <!-- Pages List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $page->slug }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $page->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $page->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('super-admin.cms-pages.edit', $page) }}" class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('super-admin.cms-pages.toggle', $page) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    <i class="fas fa-toggle-{{ $page->status === 'published' ? 'on' : 'off' }}"></i>
                                    {{ $page->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('super-admin.cms-pages.destroy', $page) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                            <p>No CMS pages created yet.</p>
                            <a href="{{ route('super-admin.cms-pages.create') }}" class="text-primary-600 hover:text-primary-700">Create your first page</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pages->links() }}
</div>
@endsection
