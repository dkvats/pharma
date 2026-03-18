@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h1>
            <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Products
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Basic Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <input type="text" id="category" name="category" value="{{ old('category', $product->category) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror"
                                placeholder="e.g., Medicine, Supplement, Equipment">
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sku') border-red-500 @enderror"
                                placeholder="Unique product code">
                            @error('sku')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                            <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror">
                            @error('brand')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company/Manufacturer</label>
                            <input type="text" id="company" name="company" value="{{ old('company', $product->company) }}"
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
                            <input type="number" id="mrp" name="mrp" value="{{ old('mrp', $product->mrp) }}" step="0.01" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('mrp') border-red-500 @enderror"
                                placeholder="Maximum Retail Price">
                            @error('mrp')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-1">Discount (₹) *</label>
                            <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $product->discount_amount) }}" step="0.01" min="0" required
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
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 @error('price') border-red-500 @enderror"
                                placeholder="Selling Price">
                            @error('price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gst_percent" class="block text-sm font-medium text-gray-700 mb-1">GST (%)</label>
                            <input type="number" id="gst_percent" name="gst_percent" value="{{ old('gst_percent', $product->gst_percent) }}" step="0.01" min="0" max="100"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gst_percent') border-red-500 @enderror"
                                placeholder="e.g., 18">
                            @error('gst_percent')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="commission" class="block text-sm font-medium text-gray-700 mb-1">Commission (₹) *</label>
                            <input type="number" id="commission" name="commission" value="{{ old('commission', $product->commission) }}" step="0.01" min="0" required
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
                            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror">
                            @error('stock')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="low_stock_alert" class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert</label>
                            <input type="number" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert) }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('low_stock_alert') border-red-500 @enderror"
                                placeholder="Alert threshold">
                            @error('low_stock_alert')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-1">Unit Type</label>
                            <input type="text" id="unit_type" name="unit_type" value="{{ old('unit_type', $product->unit_type) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('unit_type') border-red-500 @enderror"
                                placeholder="e.g., Box, Bottle, Strip">
                            @error('unit_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                            <input type="text" id="batch_number" name="batch_number" value="{{ old('batch_number', $product->batch_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('batch_number') border-red-500 @enderror">
                            @error('batch_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                            <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expiry_date') border-red-500 @enderror">
                            @error('expiry_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Images</h3>
                    
                    <!-- Current Main Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Main Image</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded-lg">
                            </div>
                        @else
                            <p class="text-gray-500 text-sm mb-2">No main image uploaded</p>
                        @endif
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Change Main Image</label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG, WEBP (max 2MB)</p>
                        @error('image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gallery Images -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                        @if($product->images->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($product->images as $img)
                                    <div class="relative">
                                        <img src="{{ $img->image_url }}" alt="Gallery" class="h-20 w-20 object-cover rounded-lg">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm mb-2">No gallery images</p>
                        @endif
                        <label for="gallery" class="block text-sm font-medium text-gray-700 mb-1">Add Gallery Images</label>
                        <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gallery') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Select multiple images (max 2MB each)</p>
                        @error('gallery')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Options -->
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_prescription" value="1" {{ old('requires_prescription', $product->requires_prescription) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-medium text-blue-800">Requires Prescription</span>
                        </label>
                        <p class="text-xs text-blue-600 mt-1">Customers must upload a prescription to order this product.</p>
                    </div>

                    <div class="p-4 {{ $product->is_special_spin_product ? 'bg-green-50 border-green-200' : 'bg-purple-50 border-purple-200' }} rounded-lg border">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_special_spin_product" value="1" {{ old('is_special_spin_product', $product->is_special_spin_product) ? 'checked' : '' }}
                                class="rounded border-gray-300 {{ $product->is_special_spin_product ? 'text-green-600' : 'text-purple-600' }} shadow-sm focus:ring focus:ring-opacity-50">
                            <span class="ml-2 text-sm font-semibold {{ $product->is_special_spin_product ? 'text-green-800' : 'text-purple-800' }}">
                                🎯 Special Spin Product
                                @if($product->is_special_spin_product)
                                    <span class="text-green-600 ml-2">(Currently Active)</span>
                                @endif
                            </span>
                        </label>
                        <p class="text-xs {{ $product->is_special_spin_product ? 'text-green-600' : 'text-purple-600' }} mt-1">
                            Required product for doctors to unlock spin wheel. Only one active at a time.
                        </p>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Batch Management -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Batch Management</h3>

                    @if($product->batches->count())
                    <div class="overflow-x-auto mb-4">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Batch #</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mfg Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">MRP</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->batches->sortBy('expiry_date') as $batch)
                                <tr>
                                    <td class="px-4 py-2 font-mono font-semibold text-gray-800">{{ $batch->batch_number }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $batch->manufacture_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $batch->expiry_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2">{{ $batch->quantity }}</td>
                                    <td class="px-4 py-2">₹{{ number_format($batch->mrp, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $batch->getExpiryBadgeClass() }}">
                                            {{ $batch->getExpiryStatusText() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('admin.products.batches.destroy', [$product, $batch]) }}" method="POST"
                                              onsubmit="return confirm('Remove batch {{ $batch->batch_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Total Active Stock</td>
                                    <td colspan="4" class="px-4 py-2 font-bold text-gray-800">
                                        {{ $product->batches->filter(fn($b) => $b->expiry_date->isFuture())->sum('quantity') }} units
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                        <p class="text-sm text-gray-500 mb-4">No batches added yet. Add the first batch below.</p>
                    @endif

                    <!-- Add New Batch -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3">Add New Batch</h4>
                        <form method="POST" action="{{ route('admin.products.batches.store', $product) }}">
                            @csrf
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch Number *</label>
                                    <input type="text" name="batch_number" value="{{ old('batch_number') }}"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="e.g., BT-2024-001" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Manufacture Date</label>
                                    <input type="date" name="manufacture_date" value="{{ old('manufacture_date') }}"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Expiry Date *</label>
                                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                                    <input type="number" name="quantity" value="{{ old('quantity') }}" min="1"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Units in batch" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">MRP (₹) *</label>
                                    <input type="number" name="mrp" value="{{ old('mrp', $product->mrp) }}" step="0.01" min="0"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Supplier</label>
                                    <select name="supplier_id" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-1.5 px-4 rounded-lg">
                                        + Add Batch
                                    </button>
                                </div>
                            </div>
                            @if($errors->has('batch_number') || $errors->has('expiry_date') || $errors->has('quantity') || $errors->has('mrp'))
                                <div class="mt-2 text-xs text-red-600">
                                    {{ $errors->first('batch_number') }}
                                    {{ $errors->first('expiry_date') }}
                                    {{ $errors->first('quantity') }}
                                    {{ $errors->first('mrp') }}
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Store Inventory Distribution -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Store Inventory Distribution</h3>
                    <p class="text-sm text-gray-500 mb-4">Allocate batch quantities to specific stores. Store stock deducts from the central warehouse batch.</p>

                    @foreach($product->batches->sortBy('expiry_date') as $batch)
                    <div class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-4 py-2 flex justify-between items-center">
                            <div>
                                <span class="font-mono font-semibold text-gray-800 text-sm">{{ $batch->batch_number }}</span>
                                <span class="ml-2 text-xs text-gray-500">Expiry: {{ $batch->expiry_date->format('d M Y') }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-600">Warehouse: <strong>{{ $batch->quantity }}</strong> units</span>
                                <span class="ml-3 text-xs {{ $batch->getExpiryBadgeClass() }} px-2 py-0.5 rounded font-semibold">
                                    {{ $batch->getExpiryStatusText() }}
                                </span>
                            </div>
                        </div>

                        @if($batch->storeInventories->count())
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 border-t border-gray-200">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Store</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Allocated Qty</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($batch->storeInventories as $alloc)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-gray-800">{{ $alloc->store->name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-2 text-gray-700">{{ $alloc->quantity }}</td>
                                    <td class="px-4 py-2 text-right">
                                        <form action="{{ route('admin.allocations.destroy', $alloc) }}" method="POST"
                                              onsubmit="return confirm('Remove this allocation?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        <!-- Allocate to store form -->
                        <div class="px-4 py-3 border-t border-gray-200 bg-white">
                            <form action="{{ route('admin.batches.allocate', $batch) }}" method="POST" class="flex gap-3 items-end flex-wrap">
                                @csrf
                                <div class="flex-1 min-w-[160px]">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Store</label>
                                    <select name="store_id"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select Store...</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-28">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Quantity</label>
                                    <input type="number" name="quantity" min="1" max="{{ $batch->quantity }}"
                                        class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Qty" required>
                                </div>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-1.5 px-4 rounded-lg">
                                    Allocate
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    @if($product->batches->count() === 0)
                        <p class="text-sm text-gray-500">No batches available. Add batches above first, then allocate to stores.</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Update Product
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
