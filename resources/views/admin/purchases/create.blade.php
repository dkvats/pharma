@extends('layouts.app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Create Purchase Order</h1>
            <a href="{{ route('admin.purchases.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Purchases</a>
        </div>

        <form method="POST" action="{{ route('admin.purchases.store') }}" id="purchaseForm">
            @csrf

            <!-- Order Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Order Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" required class="w-full px-3 py-2 border rounded-lg @error('supplier_id') border-red-500 @enderror">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                        <input type="date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-2 border rounded-lg @error('order_date') border-red-500 @enderror">
                        @error('order_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-semibold text-gray-800">Items</h3>
                    <button type="button" onclick="addItem()" class="bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded">+ Add Item</button>
                </div>

                <div id="itemsContainer">
                    <!-- Items will be added here -->
                </div>

                @error('items')<p class="text-red-500 text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.purchases.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-6 rounded-lg">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg">Create Purchase Order</button>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 0;
const products = @json($products);

function addItem() {
    const container = document.getElementById('itemsContainer');
    const div = document.createElement('div');
    div.className = 'item-row border rounded-lg p-4 mb-4 bg-gray-50';
    div.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="font-semibold text-gray-700">Item #${itemCount + 1}</span>
            <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Product *</label>
                <select name="items[${itemCount}][product_id]" required class="w-full px-2 py-1.5 text-sm border rounded">
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="items[${itemCount}][quantity]" min="1" required
                    class="w-full px-2 py-1.5 text-sm border rounded" placeholder="Qty">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">MRP (₹) *</label>
                <input type="number" name="items[${itemCount}][mrp]" step="0.01" min="0" required
                    class="w-full px-2 py-1.5 text-sm border rounded" placeholder="MRP">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Batch #</label>
                <input type="text" name="items[${itemCount}][batch_number]"
                    class="w-full px-2 py-1.5 text-sm border rounded" placeholder="Batch number">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="items[${itemCount}][expiry_date]"
                    class="w-full px-2 py-1.5 text-sm border rounded">
            </div>
        </div>
    `;
    container.appendChild(div);
    itemCount++;
}

// Add first item by default
addItem();
</script>
@endsection
