@extends('layouts.master')

@section('title', 'Homepage Slides – Image Slider CMS')

@section('content')
<div class="p-6 space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Homepage Slides</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the image slider shown at the top of the homepage.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ url('/') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-external-link-alt"></i> Preview Homepage
            </a>
            <a href="{{ route('admin.homepage-slides.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus"></i> Add Slide
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Info Banner --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl px-5 py-4 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
        <div class="text-sm text-blue-800">
            <strong>How the slider works:</strong> Active slides are shown at the top of the homepage, rotating every 4 seconds.
            Drag rows to reorder. Only <strong>active</strong> slides are visible to visitors. Recommended image size: <strong>1920 × 600 px</strong> (JPG, PNG, or WebP for best performance, max 4 MB).
        </div>
    </div>

    {{-- Slides Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">All Slides ({{ $slides->count() }})</h2>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Drag rows to reorder</span>
        </div>

        @if($slides->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wide">
                        <th class="px-4 py-3 w-8"></th>
                        <th class="px-4 py-3 w-12 text-center">Order</th>
                        <th class="px-4 py-3 w-28 text-center">Image</th>
                        <th class="px-4 py-3 text-left">Title / Subtitle</th>
                        <th class="px-4 py-3 text-left">Button</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-slides" class="divide-y divide-gray-100">
                    @foreach($slides as $slide)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $slide->id }}">

                        {{-- Drag handle --}}
                        <td class="px-4 py-4 cursor-grab text-gray-300 hover:text-gray-500">
                            <i class="fas fa-grip-vertical text-lg"></i>
                        </td>

                        {{-- Sort order --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 text-gray-700 font-bold rounded-full text-xs sort-badge">
                                {{ $slide->sort_order }}
                            </span>
                        </td>

                        {{-- Thumbnail --}}
                        <td class="px-4 py-4 text-center">
                            @if($slide->image_url)
                                <img src="{{ $slide->image_url }}"
                                     alt="{{ $slide->title ?? 'Slide' }}"
                                     class="w-24 h-14 object-cover rounded-lg border border-gray-200 inline-block shadow-sm">
                            @else
                                <div class="w-24 h-14 bg-gradient-to-br from-green-100 to-teal-100 rounded-lg border border-gray-200 inline-flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-xl"></i>
                                </div>
                            @endif
                        </td>

                        {{-- Title / Subtitle --}}
                        <td class="px-4 py-4">
                            <div class="font-semibold text-gray-900 text-sm">{{ $slide->title ?? '(No title)' }}</div>
                            @if($slide->subtitle)
                                <div class="text-gray-400 text-xs mt-0.5 line-clamp-1">{{ Str::limit($slide->subtitle, 70) }}</div>
                            @endif
                        </td>

                        {{-- Button --}}
                        <td class="px-4 py-4">
                            @if($slide->button_text)
                                <span class="inline-flex items-center gap-1 bg-green-50 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                    <i class="fas fa-link text-[10px]"></i> {{ $slide->button_text }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Status toggle --}}
                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="{{ route('admin.homepage-slides.toggle', $slide) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                                        {{ $slide->status === 'active'
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <i class="fas {{ $slide->status === 'active' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    {{ ucfirst($slide->status) }}
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.homepage-slides.edit', $slide) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.homepage-slides.destroy', $slide) }}"
                                      onsubmit="return confirm('Delete this slide? The image will also be removed.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100 transition-colors">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-images text-5xl mb-4 block opacity-30"></i>
            <p class="text-lg font-medium mb-1">No slides yet.</p>
            <p class="text-sm mb-4">The homepage will show a default placeholder until you add slides.</p>
            <a href="{{ route('admin.homepage-slides.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus"></i> Add Your First Slide
            </a>
        </div>
        @endif
    </div>

</div>

{{-- SortableJS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('sortable-slides');
    if (!tbody) return;

    Sortable.create(tbody, {
        handle: '.fa-grip-vertical',
        animation: 150,
        onEnd: function () {
            var rows  = tbody.querySelectorAll('tr[data-id]');
            var order = [];
            rows.forEach(function (row, index) {
                var newOrder = index + 1;
                order.push({ id: row.dataset.id, sort_order: newOrder });
                var badge = row.querySelector('.sort-badge');
                if (badge) badge.textContent = newOrder;
            });

            fetch('{{ route('admin.homepage-slides.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    tbody.style.opacity = '0.6';
                    setTimeout(function () { tbody.style.opacity = '1'; }, 300);
                }
            });
        }
    });
});
</script>
@endsection
