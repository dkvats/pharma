@extends('layouts.app')

@section('title', $store->store_name)
@section('page-title', 'Store Details')
@section('page-description', 'View store information and status')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center">
            <a href="{{ route('mr.stores.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $store->store_name }}</h1>
                <p class="text-gray-500 mt-1">Store Code: {{ $store->store_code }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if($store->isPending() || $store->isRejected())
                <a href="{{ route('mr.stores.edit', $store) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit Store
                </a>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="rounded-xl p-4 {{ $store->isApproved() ? 'bg-green-50 border border-green-200' : ($store->isRejected() ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($store->isApproved())
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                @elseif($store->isRejected())
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-600">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                @endif
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium {{ $store->isApproved() ? 'text-green-800' : ($store->isRejected() ? 'text-red-800' : 'text-yellow-800') }}">
                    Status: {{ ucfirst($store->status) }}
                </h3>
                <div class="mt-1 text-sm {{ $store->isApproved() ? 'text-green-700' : ($store->isRejected() ? 'text-red-700' : 'text-yellow-700') }}">
                    @if($store->isApproved())
                        <p>This store has been approved and is active.</p>
                        @if($store->approved_at)
                            <p class="text-xs mt-1">Approved on {{ $store->approved_at->format('M d, Y') }}</p>
                        @endif
                    @elseif($store->isRejected())
                        <p>This store registration was rejected.</p>
                        @if($store->rejection_reason)
                            <p class="text-xs mt-1">Reason: {{ $store->rejection_reason }}</p>
                        @endif
                    @else
                        <p>This store is pending admin approval.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Store Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Store Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->store_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Owner Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->owner_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Store Code</label>
                        <p class="mt-1 text-sm text-gray-900 font-mono">{{ $store->store_code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->phone }}</p>
                    </div>
                    @if($store->email)
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->email }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Address</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Full Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $store->address }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">City</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->city ?? ($store->city?->name ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">District</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->district ?? ($store->district?->name ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">State</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->state ?? ($store->state?->name ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">PIN Code</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $store->pincode }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Registration Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Registered By</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $store->mr?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Registration Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $store->created_at->format('M d, Y') }}</p>
                </div>
                @if($store->user)
                <div>
                    <label class="block text-sm font-medium text-gray-500">Store User Account</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $store->user->name }} ({{ $store->user->email }})</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
