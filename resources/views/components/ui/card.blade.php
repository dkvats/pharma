@props(['title' => null, 'icon' => null, 'color' => 'blue', 'footer' => null, 'class' => ''])

@php
$colors = [
    'blue' => 'bg-blue-50 border-blue-200',
    'green' => 'bg-green-50 border-green-200',
    'yellow' => 'bg-yellow-50 border-yellow-200',
    'red' => 'bg-red-50 border-red-200',
    'purple' => 'bg-purple-50 border-purple-200',
    'gray' => 'bg-gray-50 border-gray-200',
];
$colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden {{ $class }}">
    @if($title)
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between {{ $colorClass }}">
            <div class="flex items-center space-x-3">
                @if($icon)
                    <i class="fas {{ $icon }} text-gray-600"></i>
                @endif
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
            </div>
            {{ $header ?? '' }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
