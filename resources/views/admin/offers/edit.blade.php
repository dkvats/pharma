@extends('layouts.app')

@section('title', 'Edit Offer')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Offer</h1>
            <a href="{{ route('admin.offers.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Offers
            </a>
        </div>

        <form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <!-- Featured Image Upload -->
            <div class="mb-4">
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Offer Image</label>
                @if($offer->featured_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $offer->featured_image) }}" alt="Current offer image" class="h-32 w-auto rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Current image</p>
                    </div>
                @endif
                <input type="file" id="featured_image" name="featured_image" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('featured_image') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Upload a new image to replace (JPG, PNG, max 2MB)</p>
                @error('featured_image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Offer Title *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $offer->title) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $offer->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="offer_type" class="block text-sm font-medium text-gray-700 mb-1">Offer Type *</label>
                    <select id="offer_type" name="offer_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="ongoing" {{ old('offer_type', $offer->offer_type) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="daily" {{ old('offer_type', $offer->offer_type) == 'daily' ? 'selected' : '' }}>Offer of the Day</option>
                    </select>
                </div>

                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-1">Discount Type *</label>
                    <select id="discount_type" name="discount_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="percentage" {{ old('discount_type', $offer->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed_amount" {{ old('discount_type', $offer->discount_type) == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Discount Value *</label>
                <input type="number" id="discount_value" name="discount_value" value="{{ old('discount_value', $offer->discount_value) }}" required min="0" step="0.01"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('discount_value') border-red-500 @enderror">
                @error('discount_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $offer->start_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $offer->end_date?->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Products (Optional)</label>
                <div class="max-h-48 overflow-y-auto border border-gray-300 rounded-lg p-3">
                    @foreach($products as $product)
                        <label class="flex items-center mb-2">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" 
                                {{ in_array($product->id, old('products', $offer->products->pluck('id')->toArray())) ? 'checked' : '' }}
                                class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm">{{ $product->name }} - ₹{{ number_format($product->price, 2) }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Leave empty to apply to all products</p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }}
                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.offers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                    Update Offer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
