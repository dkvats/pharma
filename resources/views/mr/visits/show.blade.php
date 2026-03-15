@extends('layouts.app')

@section('title', 'Visit Details')
@section('page-title', 'Visit Report Details')
@section('page-description', 'View doctor visit information')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center">
            <a href="{{ route('mr.visits.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Visit Report</h1>
                <p class="text-gray-500 mt-1">{{ $visit->doctor?->name ?? 'Unknown Doctor' }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('mr.visits.edit', $visit) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Visit
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="rounded-xl p-4 {{ $visit->status === 'completed' ? 'bg-green-50 border border-green-200' : ($visit->status === 'cancelled' ? 'bg-red-50 border border-red-200' : ($visit->status === 'planned' ? 'bg-blue-50 border border-blue-200' : 'bg-yellow-50 border border-yellow-200')) }}">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($visit->status === 'completed')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                @elseif($visit->status === 'cancelled')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                @elseif($visit->status === 'planned')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium {{ $visit->status === 'completed' ? 'text-green-800' : ($visit->status === 'cancelled' ? 'text-red-800' : ($visit->status === 'planned' ? 'text-blue-800' : 'text-yellow-800')) }}">
                    Status: {{ ucfirst($visit->status) }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Visit Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Doctor Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Doctor Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Doctor Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->doctor?->name ?? 'N/A' }}</p>
                </div>
                @if($visit->doctor?->specialization)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Specialization</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->doctor->specialization }}</p>
                </div>
                @endif
                @if($visit->doctor?->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->doctor->phone }}</p>
                </div>
                @endif
                @if($visit->doctor?->address)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->doctor->address }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Visit Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Visit Details</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Visit Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $visit->visit_date?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Visit Time</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $visit->visit_time?->format('h:i A') ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($visit->next_visit_date)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Next Visit Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->next_visit_date->format('M d, Y') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Products Discussed -->
    @if($visit->products_discussed)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Products Discussed</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $visit->products_discussed }}</p>
        </div>
    </div>
    @endif

    <!-- Remarks -->
    @if($visit->remarks)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Remarks</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $visit->remarks }}</p>
        </div>
    </div>
    @endif

    <!-- Visit Photo -->
    @if($visit->photo_path)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Visit Photo</h2>
        </div>
        <div class="p-6">
            <img src="{{ Storage::url($visit->photo_path) }}" alt="Visit Photo" class="max-w-full h-auto rounded-lg">
        </div>
    </div>
    @endif

    <!-- Metadata -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Visit Metadata</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Reported By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->mr?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $visit->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
