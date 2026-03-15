@extends('layouts.master')

@section('title', 'Platform Features – Homepage CMS')

@section('content')
<div class="p-6 space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Platform Features</h1>
            <p class="text-sm text-gray-500 mt-1">Manage the feature cards shown on the homepage Features section.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ url('/') }}#features" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-external-link-alt"></i> Preview
            </a>
            <a href="{{ route('admin.homepage-features.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus"></i> Add Feature
            </a>
        </div>
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

    {{-- Features Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800">All Features ({{ $features->count() }})</h2>
            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Drag rows to reorder</span>
        </div>

        @if($features->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wide">
                        <th class="px-4 py-3 w-8"></th>
                        <th class="px-4 py-3 w-12 text-center">Order</th>
                        <th class="px-4 py-3 w-16 text-center">Icon</th>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-features" class="divide-y divide-gray-100">
                    @foreach($features as $feature)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $feature->id }}">
                        {{-- Drag handle --}}
                        <td class="px-4 py-4 cursor-grab text-gray-300 hover:text-gray-500">
                            <i class="fas fa-grip-vertical text-lg"></i>
                        </td>
                        {{-- Sort order --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 text-gray-700 font-bold rounded-full text-xs sort-badge">
                                {{ $feature->sort_order }}
                            </span>
                        </td>
                        {{-- Icon --}}
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-10 h-10 {{ $feature->icon_bg_class }} rounded-xl">
                                <i class="fas fa-{{ $feature->icon }} {{ $feature->icon_text_class }} text-lg"></i>
                            </span>
                        </td>
                        {{-- Title --}}
                        <td class="px-4 py-4">
                            <span class="font-semibold text-gray-900">{{ $feature->title }}</span>
                        </td>
                        {{-- Description --}}
                        <td class="px-4 py-4">
                            <span class="text-gray-500 text-xs line-clamp-2">{{ Str::limit($feature->description, 80) }}</span>
                        </td>
                        {{-- Status --}}
                        <td class="px-4 py-4 text-center">
                            <form method="POST" action="{{ route('admin.homepage-features.toggle', $feature) }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-colors
                                        {{ $feature->status === 'active'
                                            ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    <i class="fas {{ $feature->status === 'active' ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    {{ ucfirst($feature->status) }}
                                </button>
                            </form>
                        </td>
                        {{-- Actions --}}
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.homepage-features.edit', $feature) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.homepage-features.destroy', $feature) }}" onsubmit="return confirm('Delete this feature?')">
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
            <i class="fas fa-star text-5xl mb-4 block opacity-30"></i>
            <p class="text-lg">No features yet.</p>
            <a href="{{ route('admin.homepage-features.create') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                Add your first feature
            </a>
        </div>
        @endif
    </div>

</div>

{{-- SortableJS --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('sortable-features');
    if (!tbody) return;

    Sortable.create(tbody, {
        handle: '.fa-grip-vertical',
        animation: 150,
        onEnd: function () {
            const rows = tbody.querySelectorAll('tr[data-id]');
            const order = [];
            rows.forEach((row, index) => {
                const newOrder = index + 1;
                order.push({ id: row.dataset.id, sort_order: newOrder });
                const badge = row.querySelector('.sort-badge');
                if (badge) badge.textContent = newOrder;
            });

            fetch('{{ route('admin.homepage-features.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    tbody.style.opacity = '0.6';
                    setTimeout(() => tbody.style.opacity = '1', 300);
                }
            });
        }
    });
});
</script>
@endsection
