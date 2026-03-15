@extends('layouts.master')

@section('title', 'Homepage Manager – Admin CMS')

@section('content')
<div class="p-6 space-y-8">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Homepage Manager</h1>
            <p class="text-sm text-gray-500 mt-1">Control every section of the public homepage.</p>
        </div>
        <a href="{{ url('/') }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-external-link-alt"></i> Preview Homepage
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i>{{ session('error') }}
        </div>
    @endif

    {{-- ===== BRANDING SETTINGS ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-palette text-blue-600"></i> Branding Settings
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">Site name, logo, and favicon shown across the homepage.</p>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.homepage-manager.branding') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Site Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Pharma Distribution Network" required>
                    </div>

                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <input type="file" name="logo" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if($settings->logo_url)
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ $settings->logo_url }}" alt="Logo" class="h-10 w-auto rounded border">
                                <span class="text-xs text-gray-400">Current logo</span>
                            </div>
                        @endif
                    </div>

                    {{-- Favicon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon (ICO/PNG)</label>
                        <input type="file" name="favicon" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @if(!empty($settings->favicon))
                            <div class="mt-2 flex items-center gap-2">
                                <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="h-8 w-8 rounded border">
                                <span class="text-xs text-gray-400">Current favicon</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-1"></i> Save Branding
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== SECTIONS MANAGER ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-600"></i> Homepage Sections
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Enable/disable sections, edit content, and drag to reorder.</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Drag rows to reorder</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wide">
                        <th class="px-4 py-3 w-8"></th>
                        <th class="px-4 py-3 text-left">Order</th>
                        <th class="px-4 py-3 text-left">Section</th>
                        <th class="px-4 py-3 text-left">Key</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-sections" class="divide-y divide-gray-100">
                    @foreach($sections as $section)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $section->id }}">
                        {{-- Drag handle --}}
                        <td class="px-4 py-4 cursor-grab text-gray-300 hover:text-gray-500">
                            <i class="fas fa-grip-vertical text-lg"></i>
                        </td>
                        {{-- Sort order badge --}}
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-700 font-bold rounded-full text-xs sort-badge">
                                {{ $section->sort_order }}
                            </span>
                        </td>
                        {{-- Title --}}
                        <td class="px-4 py-4">
                            <div class="font-semibold text-gray-900">{{ $section->section_title }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $section->contents->count() }} content fields</div>
                        </td>
                        {{-- Key --}}
                        <td class="px-4 py-4">
                            <code class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded font-mono">{{ $section->section_key }}</code>
                        </td>
                        {{-- Status toggle --}}
                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="{{ route('admin.homepage-manager.toggle', $section) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                                        {{ $section->status === 'active'
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <i class="fas {{ $section->status === 'active' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    {{ $section->status === 'active' ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-4 text-right">
                            <a href="{{ route('admin.homepage-manager.edit', $section) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-edit"></i> Edit Content
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Sortable JS via CDN (SortableJS) --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortable-sections');

    Sortable.create(tbody, {
        handle: '.fa-grip-vertical',
        animation: 150,
        onEnd: function () {
            const rows = tbody.querySelectorAll('tr[data-id]');
            const order = [];
            rows.forEach((row, index) => {
                const newOrder = index + 1;
                order.push({ id: row.dataset.id, sort_order: newOrder });
                // Update badge
                const badge = row.querySelector('.sort-badge');
                if (badge) badge.textContent = newOrder;
            });

            // POST to reorder endpoint
            fetch('{{ route('admin.homepage-manager.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    // Brief flash on success
                    tbody.style.opacity = '0.6';
                    setTimeout(() => tbody.style.opacity = '1', 300);
                }
            });
        }
    });
});
</script>
@endsection
