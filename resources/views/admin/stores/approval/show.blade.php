@extends('layouts.app')

@section('title', 'Store Details')
@section('page-title', 'Store Approval Details')
@section('page-description', 'Review and manage store registration')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.stores.approval.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Store Details</h1>
            <p class="text-gray-500">Review store registration information</p>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-6 p-4 rounded-xl {{ $store->isPending() ? 'bg-yellow-50 border border-yellow-200' : ($store->isApproved() ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200') }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full {{ $store->isPending() ? 'bg-yellow-100' : ($store->isApproved() ? 'bg-green-100' : 'bg-red-100') }} flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $store->isPending() ? 'text-yellow-600' : ($store->isApproved() ? 'text-green-600' : 'text-red-600') }}">
                        @if($store->isPending())
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        @elseif($store->isApproved())
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        @else
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        @endif
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Current Status</p>
                    <p class="text-lg font-semibold {{ $store->isPending() ? 'text-yellow-800' : ($store->isApproved() ? 'text-green-800' : 'text-red-800') }}">
                        {{ ucfirst($store->status) }}
                    </p>
                </div>
            </div>
            @if($store->approved_at)
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ $store->isApproved() ? 'Approved' : 'Processed' }} On</p>
                    <p class="text-sm font-medium">{{ $store->approved_at->format('M d, Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Store Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Store Information</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Store Name</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->store_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Owner Name</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->owner_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Store Code</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->store_code }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Phone</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->phone }}</p>
            </div>
            @if($store->email)
            <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->email }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-500">Registered By</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->mr?->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Location Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Location Information</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Address</label>
                <p class="mt-1 text-base text-gray-900">{{ $store->address }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Pincode</label>
                <p class="mt-1 text-base font-medium text-gray-900">{{ $store->pincode }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">State</label>
                <p class="mt-1 text-base text-gray-900">{{ $store->state ?? $store->state?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">District</label>
                <p class="mt-1 text-base text-gray-900">{{ $store->district ?? $store->district?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">City</label>
                <p class="mt-1 text-base text-gray-900">{{ $store->city ?? $store->city?->name ?? 'N/A' }}</p>
            </div>
            @if($store->area || $store->area?->name)
            <div>
                <label class="block text-sm font-medium text-gray-500">Area</label>
                <p class="mt-1 text-base text-gray-900">{{ $store->area ?? $store->area?->name ?? 'N/A' }}</p>
            </div>
            @endif
        </div>
    </div>

    @if($store->isRejected() && $store->rejection_reason)
    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
        <h3 class="text-sm font-medium text-red-800 mb-2">Rejection Reason</h3>
        <p class="text-red-700">{{ $store->rejection_reason }}</p>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-3">
        @if($store->isPending())
            <form action="{{ route('admin.stores.approval.approve', $store) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Approve Store
                </button>
            </form>
            
            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Reject Store
            </button>
        @elseif($store->isApproved())
            <form action="{{ route('admin.stores.approval.deactivate', $store) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
                        <line x1="12" y1="2" x2="12" y2="12"></line>
                    </svg>
                    Deactivate Store
                </button>
            </form>
        @elseif($store->status === 'inactive')
            <form action="{{ route('admin.stores.approval.reactivate', $store) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M1 4v6h6"></path>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                    </svg>
                    Reactivate Store
                </button>
            </form>
        @endif
        
        <a href="{{ route('admin.stores.approval.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
            Back to List
        </a>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Store Registration</h3>
        <form action="{{ route('admin.stores.approval.reject', $store) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                    placeholder="Provide reason for rejection..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Confirm Rejection
                </button>
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" 
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
