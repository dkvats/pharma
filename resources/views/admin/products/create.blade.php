@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
            <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Products
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror"
                        placeholder="e.g., Medicine, Supplement, Equipment">
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="mrp" class="block text-sm font-medium text-gray-700 mb-1">MRP (₹) *</label>
                        <input type="number" id="mrp" name="mrp" value="{{ old('mrp', 0) }}" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('mrp') border-red-500 @enderror"
                            placeholder="Maximum Retail Price">
                        @error('mrp')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Discount (₹) *</label>
                        <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('discount_amount') border-red-500 @enderror"
                            placeholder="Discount Amount">
                        @error('discount_amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Final Price (₹) *
                            <span id="price-auto-badge" class="ml-1 text-xs font-normal text-green-600 bg-green-50 border border-green-200 rounded px-1.5 py-0.5 hidden">Auto-calculated</span>
                        </label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-500 @enderror"
                            placeholder="Selling Price (MRP - Discount)">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">Auto-filled as MRP − Discount. You can override manually.</p>
                    </div>
                    <div>
                        <label for="commission" class="block text-sm font-medium text-gray-700 mb-1">Commission (₹) *</label>
                        <input type="number" id="commission" name="commission" value="{{ old('commission', 0) }}" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('commission') border-red-500 @enderror">
                        @error('commission')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror">
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                    <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG, WEBP (max 2MB)</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_prescription" value="1" {{ old('requires_prescription') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Requires Prescription</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">If checked, customers must upload a prescription to order this product.</p>
                </div>

                <div class="mb-4 p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_special_spin_product" value="1" {{ old('is_special_spin_product') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-semibold text-purple-800">🎯 Mark as Special Spin Product</span>
                    </label>
                    <p class="text-xs text-purple-600 mt-1">If checked, this product becomes the required product for doctors to unlock the spin wheel. Only one product can be active at a time.</p>
                </div>

                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var mrpInput      = document.getElementById('mrp');
    var discountInput = document.getElementById('discount_amount');
    var priceInput    = document.getElementById('price');
    var autoBadge     = document.getElementById('price-auto-badge');

    function calcPrice() {
        var mrp      = parseFloat(mrpInput.value)      || 0;
        var discount = parseFloat(discountInput.value) || 0;
        var computed = Math.max(0, mrp - discount);
        priceInput.value = computed.toFixed(2);
        autoBadge.classList.remove('hidden');
    }

    mrpInput.addEventListener('input', calcPrice);
    discountInput.addEventListener('input', calcPrice);

    // Let admin override price manually — hide badge while typing in price
    priceInput.addEventListener('input', function () {
        autoBadge.classList.add('hidden');
    });
})();
</script>
@endsection
