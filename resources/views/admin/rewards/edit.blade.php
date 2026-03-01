@extends('layouts.app')

@section('title', 'Edit Reward')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Reward: {{ $reward->name }}</h1>
            <a href="{{ route('admin.rewards.index') }}" class="text-blue-600 hover:text-blue-800">
                &larr; Back to Rewards
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.rewards.update', $reward) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($reward->image)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Image</label>
                    <img src="{{ asset('storage/' . $reward->image) }}" alt="{{ $reward->name }}" class="h-32 w-32 object-cover rounded-lg">
                </div>
                @endif

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">{{ $reward->image ? 'Change' : 'Upload' }} Reward Image</label>
                    <input type="file" id="image" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror">
                    <p class="text-gray-500 text-sm mt-1">Upload a new image to replace the current one (optional)</p>
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Reward Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $reward->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $reward->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select id="type" name="type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror">
                            <option value="cash" {{ old('type', $reward->type) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="product" {{ old('type', $reward->type) == 'product' ? 'selected' : '' }}>Product</option>
                            <option value="gift_card" {{ old('type', $reward->type) == 'gift_card' ? 'selected' : '' }}>Gift Card</option>
                            <option value="other" {{ old('type', $reward->type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value ($) *</label>
                        <input type="number" id="value" name="value" value="{{ old('value', $reward->value) }}" step="0.01" min="0" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('value') border-red-500 @enderror">
                        @error('value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="probability" class="block text-sm font-medium text-gray-700 mb-1">Probability (%) *</label>
                        <input type="number" id="probability" name="probability" value="{{ old('probability', $reward->probability) }}" step="0.01" min="0" max="100" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('probability') border-red-500 @enderror">
                        <p class="text-gray-500 text-sm mt-1">Chance of winning (0-100)</p>
                        @error('probability')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $reward->stock) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror">
                        <p class="text-gray-500 text-sm mt-1">Leave empty for unlimited</p>
                        @error('stock')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $reward->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.rewards.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Update Reward
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
