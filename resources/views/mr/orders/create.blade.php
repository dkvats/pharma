@extends('layouts.app')

@section('title', 'Create Order')
@section('page-title', 'Create New Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">New Order</h3>
        </div>
        
        <form action="{{ route('mr.orders.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <!-- Doctor Selection -->
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Select Doctor *</label>
                <select id="doctor_id" name="doctor_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('doctor_id') border-red-500 @enderror">
                    <option value="">Choose Doctor...</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name }} - {{ $doctor->clinic_name }} ({{ $doctor->area?->name }})
                        </option>
                    @endforeach
                </select>
                @error('doctor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Order Date -->
            <div>
                <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                <input type="date" id="order_date" name="order_date" value="{{ old('order_date', today()->format('Y-m-d')) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('order_date') border-red-500 @enderror">
                @error('order_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Products -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Products *</label>
                <div id="products-container" class="space-y-3">
                    <div class="product-row flex flex-col md:flex-row gap-3 p-3 bg-gray-50 rounded-lg">
                        <select name="products[0][product_id]" required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - ₹{{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <input type="number" name="products[0][quantity]" min="1" value="1" required
                            class="w-full md:w-24 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Qty">
                        <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800 px-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <button type="button" onclick="addProductRow()" class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                    + Add Another Product
                </button>
                
                @error('products')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea id="remarks" name="remarks" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('remarks') border-red-500 @enderror"
                    placeholder="Enter any additional notes...">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('mr.orders.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let productIndex = 1;

function addProductRow() {
    const container = document.getElementById('products-container');
    const row = document.createElement('div');
    row.className = 'product-row flex flex-col md:flex-row gap-3 p-3 bg-gray-50 rounded-lg';
    row.innerHTML = `
        <select name="products[${productIndex}][product_id]" required
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select Product</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                    {{ $product->name }} - ₹{{ number_format($product->price, 2) }}
                </option>
            @endforeach
        </select>
        <input type="number" name="products[${productIndex}][quantity]" min="1" value="1" required
            class="w-full md:w-24 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Qty">
        <button type="button" onclick="removeProductRow(this)" class="text-red-600 hover:text-red-800 px-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </button>
    `;
    container.appendChild(row);
    productIndex++;
}

function removeProductRow(button) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length > 1) {
        button.closest('.product-row').remove();
    } else {
        alert('At least one product is required.');
    }
}
</script>
@endpush
@endsection
