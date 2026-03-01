@extends('layouts.app')

@section('title', 'Manage Stock')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Stock</h1>
        <a href="{{ route('store.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
            Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Stock</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stocks as $stock)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $stock->product->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $stock->product->sku ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $stock->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $stock->sold_quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="text-sm font-bold {{ $stock->available_stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $stock->available_stock }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($stock->available_stock > 0)
                                <button onclick="openSaleModal({{ $stock->id }}, '{{ $stock->product->name }}', {{ $stock->available_stock }})" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-1 px-3 rounded">
                                    Record Sale
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">Out of Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No stock available. Stock will appear here when orders are delivered to your store.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stocks->count() > 0)
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2">How it works:</h3>
        <ul class="list-disc list-inside text-sm text-blue-700 space-y-1">
            <li>Stock is automatically added when admin delivers orders to your store</li>
            <li>Use "Record Sale" to record customer sales with prescription</li>
            <li>Enter doctor code to count toward doctor referral</li>
            <li>Available stock = Total stock - Sold quantity</li>
        </ul>
    </div>
    @endif
</div>

<!-- Sale Modal -->
<div id="saleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Record Customer Sale</h3>
            <p class="text-sm text-gray-600 mb-4" id="modalProductName"></p>
            
            <form id="saleForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="stockId" name="stock_id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" id="modalQuantity" value="1" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor Code or Phone (Optional)</label>
                    <input type="text" name="doctor_code" placeholder="Enter doctor code or phone number" value="{{ old('doctor_code') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('doctor_code') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Enter doctor's unique code OR registered phone number. Leave empty for store direct sale.</p>
                    @error('doctor_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prescription *</label>
                    <input type="file" name="prescription" accept=".jpg,.jpeg,.png,.pdf" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Required: JPG, PNG, PDF (max 2MB)</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSaleModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        Record Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSaleModal(stockId, productName, availableStock) {
    document.getElementById('stockId').value = stockId;
    document.getElementById('modalProductName').textContent = 'Product: ' + productName + ' (Available: ' + availableStock + ')';
    document.getElementById('modalQuantity').max = availableStock;
    document.getElementById('saleForm').action = '/store/stock/' + stockId + '/record-sale';
    document.getElementById('saleModal').classList.remove('hidden');
}

function closeSaleModal() {
    document.getElementById('saleModal').classList.add('hidden');
}
</script>
@endsection
