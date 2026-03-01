@props(['type' => 'info', 'title' => null, 'dismissible' => true])

@php
$typeClasses = [
    'info' => 'bg-blue-50 border-blue-200 text-blue-800',
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    'error' => 'bg-red-50 border-red-200 text-red-800',
];

$iconClasses = [
    'info' => 'fa-info-circle text-blue-400',
    'success' => 'fa-check-circle text-green-400',
    'warning' => 'fa-exclamation-triangle text-yellow-400',
    'error' => 'fa-exclamation-circle text-red-400',
];
@endphp

<div class="rounded-lg border p-4 {{ $typeClasses[$type] ?? $typeClasses['info'] }}" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas {{ $iconClasses[$type] ?? $iconClasses['info'] }} text-lg"></i>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium">{{ $title }}</h3>
            @endif
            <div class="text-sm {{ $title ? 'mt-2' : '' }}">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <button onclick="this.closest('[role=alert]').remove()" class="flex-shrink-0 ml-4 text-current hover:opacity-75">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</div>
