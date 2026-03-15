@extends('layouts.app')

@section('title', 'Sample Details')
@section('page-title', 'Sample Distribution Details')
@section('page-description', 'View sample record information')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center">
            <a href="{{ route('mr.samples.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sample Distribution</h1>
                <p class="text-gray-500 mt-1">{{ $sample->product?->name ?? 'Unknown Product' }} to {{ $sample->doctor?->name ?? 'Unknown Doctor' }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('mr.samples.edit', $sample) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit Sample
            </a>
        </div>
    </div>

    <!-- Sample Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Product Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Product Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Product Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->product?->name ?? 'N/A' }}</p>
                </div>
                @if($sample->product?->description)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->product->description }}</p>
                </div>
                @endif
                @if($sample->product?->price)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Price</label>
                    <p class="mt-1 text-sm text-gray-900">₹{{ number_format($sample->product->price, 2) }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Doctor Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Doctor Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Doctor Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->doctor?->name ?? 'N/A' }}</p>
                </div>
                @if($sample->doctor?->clinic_name)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Clinic Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->doctor->clinic_name }}</p>
                </div>
                @endif
                @if($sample->doctor?->specialization)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Specialization</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->doctor->specialization }}</p>
                </div>
                @endif
                @if($sample->doctor?->phone)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->doctor->phone }}</p>
                </div>
                @endif
                @if($sample->doctor?->address)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->doctor->address }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Distribution Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Distribution Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Quantity</label>
                    <p class="mt-1 text-2xl font-bold text-blue-600">{{ $sample->quantity }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Given Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->given_date?->format('M d, Y') ?? 'N/A' }}</p>
                </div>
                @if($sample->batch_no)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Batch Number</label>
                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $sample->batch_no }}</p>
                </div>
                @endif
                @if($sample->expiry_date)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                    <p class="mt-1 text-sm {{ $sample->expiry_date->isPast() ? 'text-red-600' : ($sample->expiry_date->isToday() || $sample->expiry_date->diffInDays(now()) <= 30 ? 'text-yellow-600' : 'text-gray-900') }}">
                        {{ $sample->expiry_date->format('M d, Y') }}
                        @if($sample->expiry_date->isPast())
                            <span class="text-xs">(Expired)</span>
                        @elseif($sample->expiry_date->diffInDays(now()) <= 30)
                            <span class="text-xs">(Expiring soon)</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Remarks -->
    @if($sample->remarks)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Remarks</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $sample->remarks }}</p>
        </div>
    </div>
    @endif

    <!-- Metadata -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Record Metadata</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Recorded By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->mr?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Created At</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $sample->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
