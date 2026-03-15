@extends('layouts.app')

@section('title', 'Store Cancellation Requests')
@section('page-title', 'Store Cancellation Requests')
@section('page-description', 'Review and manage store order cancellation requests')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Store Cancellation Requests</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:underline">
            &larr; Back to Orders
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.cancellation-requests.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border rounded p-2">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                <select name="store_id" class="w-full border rounded p-2">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded p-2">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.cancellation-requests.index') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Requests Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Request ID</th>
                    <th class="p-3 text-left">Order #</th>
                    <th class="p-3 text-left">Store</th>
                    <th class="p-3 text-left">Reason</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Submitted</th>
                    <th class="p-3 text-left">Reviewed By</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">#{{ $request->id }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.orders.show', $request->order) }}" class="text-blue-600 hover:underline">
                            #{{ $request->order->order_number ?? $request->order_id }}
                        </a>
                    </td>
                    <td class="p-3">{{ $request->store->name ?? 'Unknown' }}</td>
                    <td class="p-3 max-w-xs truncate" title="{{ $request->reason }}">
                        {{ Str::limit($request->reason, 50) }}
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-white text-xs
                            @if($request->status == 'pending') bg-yellow-500
                            @elseif($request->status == 'approved') bg-green-500
                            @elseif($request->status == 'rejected') bg-red-500
                            @else bg-gray-500
                            @endif">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td class="p-3">{{ $request->created_at->format('d M Y, h:i A') }}</td>
                    <td class="p-3">
                        @if($request->reviewer)
                            {{ $request->reviewer->name }}
                            <br><span class="text-xs text-gray-500">{{ $request->reviewed_at?->format('d M Y, h:i A') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($request->isPending())
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('admin.cancellation-requests.approve', $request) }}" 
                                      onsubmit="return confirm('Approve this cancellation request? The order will be cancelled and stock restored.');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                        Approve
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.cancellation-requests.reject', $request) }}" 
                                      onsubmit="return confirm('Reject this cancellation request?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-gray-400 text-xs">Reviewed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-6 text-center text-gray-500">
                        No cancellation requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection
