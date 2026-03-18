@extends('layouts.app')

@section('title', 'Store Approval')
@section('page-title', 'Store Approval Management')
@section('page-description', 'Approve or reject MR registered stores')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Store Approval</h1>
            <p class="text-gray-500 mt-1">Manage MR registered stores</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.stores.approval.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'text-gray-600 hover:bg-gray-100' }}">
                Pending ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('admin.stores.approval.index', ['status' => 'approved']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'approved' ? 'bg-green-100 text-green-800' : 'text-gray-600 hover:bg-gray-100' }}">
                Approved ({{ $counts['approved'] }})
            </a>
            <a href="{{ route('admin.stores.approval.index', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'rejected' ? 'bg-red-100 text-red-800' : 'text-gray-600 hover:bg-gray-100' }}">
                Rejected ({{ $counts['rejected'] }})
            </a>
            <a href="{{ route('admin.stores.approval.index', ['status' => 'inactive']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $status === 'inactive' ? 'bg-gray-100 text-gray-800' : 'text-gray-600 hover:bg-gray-100' }}">
                Inactive ({{ $counts['inactive'] }})
            </a>
        </div>
    </div>

    <!-- Stores Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned MR</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stores as $store)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-primary-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary-600">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $store->store_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $store->store_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $store->mr?->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $store->assignedMr?->name ?? 'Not assigned' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $store->city ?? $store->city?->name }}, {{ $store->district ?? $store->district?->name }}</div>
                            <div class="text-sm text-gray-500">{{ $store->state ?? $store->state?->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($store->isPending())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif($store->isApproved())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Approved
                                </span>
                            @elseif($store->isRejected())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $store->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            @if($store->isPending())
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.stores.approval.approve', $store) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <select name="mr_id" required class="text-sm border-gray-300 rounded-md">
                                            <option value="">Assign MR</option>
                                            @foreach($mrs as $mr)
                                                <option value="{{ $mr->id }}">{{ $mr->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.stores.approval.reject', $store) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="text-sm border-gray-300 rounded-md">
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('admin.stores.approval.show', $store) }}" class="text-primary-600 hover:text-primary-900">
                                    View Details
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-4">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                <p class="text-lg font-medium">No {{ $status }} stores found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($stores->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $stores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
