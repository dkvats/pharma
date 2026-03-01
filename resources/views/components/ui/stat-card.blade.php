@props(['title', 'value', 'icon', 'color' => 'blue', 'trend' => null, 'trendLabel' => null])

@php
$colorClasses = [
    'blue' => 'bg-blue-500',
    'green' => 'bg-green-500',
    'yellow' => 'bg-yellow-500',
    'red' => 'bg-red-500',
    'purple' => 'bg-purple-500',
    'indigo' => 'bg-indigo-500',
];
$iconBgClass = $colorClasses[$color] ?? $colorClasses['blue'];

$trendColors = [
    'up' => 'text-green-600',
    'down' => 'text-red-600',
    'neutral' => 'text-gray-600',
];
$trendIcon = [
    'up' => 'fa-arrow-up',
    'down' => 'fa-arrow-down',
    'neutral' => 'fa-minus',
];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-12 h-12 {{ $iconBgClass }} rounded-lg flex items-center justify-center">
                <i class="fas {{ $icon }} text-white text-xl"></i>
            </div>
        </div>
        <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
            
            @if($trend)
                <div class="flex items-center mt-1">
                    <i class="fas {{ $trendIcon[$trend] ?? 'fa-minus' }} {{ $trendColors[$trend] ?? 'text-gray-600' }} text-xs mr-1"></i>
                    <span class="text-xs {{ $trendColors[$trend] ?? 'text-gray-600' }}">
                        {{ $trendLabel ?? '' }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
