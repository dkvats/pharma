@extends('layouts.app')

@section('title', 'Request Cancellation')
@section('page-title', 'Request Order Cancellation')
@section('page-description', 'Submit cancellation request to Admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Request Cancellation: Order #{{ $order->order_number ?? $order->id }}
        </h1>
        <a href="{{ route('store.orders.show', $order) }}" class="text-blue-600 hover:underline">
            &larr; Back to Order
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
            <h3 class="font-semibold text-yellow-800 mb-2">Important Information</h3>
            <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                <li>This order is already approved and cannot be cancelled directly.</li>
                <li>Your cancellation request will be sent to Admin for review.</li>
                <li>The order status will remain "approved" until Admin approves or rejects your request.</li>
                <li>If approved, stock will be restored and the order will be marked as cancelled.</li>
            </ul>
        </div>

        <div class="mb-6">
            <h3 class="font-semibold mb-3">Order Summary</h3>
            <div class="bg-gray-50 rounded p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Order Number:</span>
                        <span class="font-medium">#{{ $order->order_number ?? $order->id }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="font-medium">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 rounded text-white text-xs bg-blue-500">
                            Approved
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-600">Order Date:</span>
                        <span class="font-medium">{{ $order->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('store.orders.submit-cancel-request', $order) }}">
            @csrf

            <div class="mb-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Cancellation <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="reason" 
                    name="reason" 
                    rows="4" 
                    class="w-full border rounded p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror"
                    placeholder="Please provide a detailed reason for requesting cancellation..."
                    required
                >{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('store.orders.show', $order) }}" 
                   class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
