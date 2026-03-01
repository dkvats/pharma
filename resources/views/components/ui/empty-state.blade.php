@props(['icon' => 'fa-inbox', 'title' => 'No data available', 'description' => null, 'action' => null])

<div class="text-center py-12">
    <div class="mx-auto h-12 w-12 text-gray-400">
        <i class="fas {{ $icon }} text-4xl"></i>
    </div>
    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif
    @if($action)
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
