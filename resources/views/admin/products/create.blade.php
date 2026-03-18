@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
            <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Products
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Basic Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <input type="text" id="category" name="category" value="{{ old('category') }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror"
                                placeholder="e.g., Medicine, Supplement, Equipment">
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" id="sku" name="sku" value="{{ old('sku') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror"
                                placeholder="Unique product code">
                            @error('sku')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <input type="text" id="brand" name="brand" value="{{ old('brand') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company/Manufacturer</label>
                            <input type="text" id="company" name="company" value="{{ old('company') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('company') border-red-500 @enderror">
                            @error('company')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Pricing</h3>
                    <div class="grid grid-cols-3 gap-4">
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

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                                Final Price (₹) *
                                <span id="price-auto-badge" class="ml-1 text-xs font-normal text-green-600 bg-green-50 border border-green-200 rounded px-1.5 py-0.5 hidden">Auto</span>
                            </label>
                            <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-500 @enderror"
                                placeholder="Selling Price">
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gst_percent" class="block text-sm font-medium text-gray-700 mb-1">GST (%)</label>
                            <input type="number" id="gst_percent" name="gst_percent" value="{{ old('gst_percent', 0) }}" step="0.01" min="0" max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gst_percent') border-red-500 @enderror"
                                placeholder="e.g., 18">
                            @error('gst_percent')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="commission" class="block text-sm font-medium text-gray-700 mb-1">Commission (₹) *</label>
                            <input type="number" id="commission" name="commission" value="{{ old('commission', 0) }}" step="0.01" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('commission') border-red-500 @enderror">
                            @error('commission')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Stock & Inventory -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Stock & Inventory</h3>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror">
                            @error('stock')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="low_stock_alert" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert</label>
                            <input type="number" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', 10) }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('low_stock_alert') border-red-500 @enderror"
                                placeholder="Alert threshold">
                            @error('low_stock_alert')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-1">Unit Type</label>
                            <input type="text" id="unit_type" name="unit_type" value="{{ old('unit_type') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('unit_type') border-red-500 @enderror"
                                placeholder="e.g., Box, Bottle, Strip">
                            @error('unit_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Initial Batch (optional) -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1 border-b pb-2">Initial Batch <span class="text-sm font-normal text-gray-500">(optional)</span></h3>
                    <p class="text-xs text-gray-500 mb-3">You can add the first batch now, or add batches after saving the product from the Edit page.</p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <div>
                                <label for="batch_number" class="block text-xs font-medium text-gray-700 mb-1">Batch Number</label>
                                <input type="text" id="batch_number" name="batch_number" value="{{ old('batch_number') }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('batch_number') border-red-500 @enderror"
                                    placeholder="e.g., BT-2024-001">
                                @error('batch_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="manufacture_date" class="block text-xs font-medium text-gray-700 mb-1">Manufacture Date</label>
                                <input type="date" id="manufacture_date" name="manufacture_date" value="{{ old('manufacture_date') }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="expiry_date" class="block text-xs font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-500 @enderror">
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">If you provide a batch number and expiry date, an initial batch will be created automatically using the stock quantity above.</p>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Images</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Main Product Image</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG, WEBP (max 2MB)</p>
                            @error('image')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gallery" class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                            <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gallery') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Select multiple images (max 2MB each)</p>
                            @error('gallery')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_prescription" value="1" {{ old('requires_prescription') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-blue-800">Requires Prescription</span>
                        </label>
                        <p class="text-xs text-blue-600 mt-1">Customers must upload a prescription to order this product.</p>
                    </div>

                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_special_spin_product" value="1" {{ old('is_special_spin_product') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-semibold text-purple-800">🎯 Special Spin Product</span>
                        </label>
                        <p class="text-xs text-purple-600 mt-1">Required product for doctors to unlock spin wheel. Only one active at a time.</p>
                    </div>
                </div>

                <!-- Status -->
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
