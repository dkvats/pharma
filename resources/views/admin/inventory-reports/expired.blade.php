@extends('layouts.app')

@section('title', 'Expired Products Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Expired Products</h1>
        <a href="{{ route('admin.inventory-reports.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Reports
        </a>
    </div>

    <!-- Alert -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="text-red-800 font-medium">These products have expired and should be removed from inventory.</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Expired</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($batches as $batch)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $batch->product->name }}</div>
                            <div class="text-sm text-gray-500">{{ $batch->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->batch_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $batch->expiry_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $batch->expiry_date->diffInDays(now()) }} days</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Expired
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No expired products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $batches->links() }}
    </div>
</div>
@endsection
