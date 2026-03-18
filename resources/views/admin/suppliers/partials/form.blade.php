<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name *</label>
    <input type="text" name="name" required value="{{ old('name', $supplier->name ?? '') }}" class="w-full px-3 py-2 border rounded-lg @error('name') border-red-500 @enderror">
    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="w-full px-3 py-2 border rounded-lg @error('phone') border-red-500 @enderror">
    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">GST No.</label>
    <input type="text" name="gst_no" value="{{ old('gst_no', $supplier->gst_no ?? '') }}" class="w-full px-3 py-2 border rounded-lg @error('gst_no') border-red-500 @enderror">
    @error('gst_no')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
    <textarea name="address" rows="3" class="w-full px-3 py-2 border rounded-lg @error('address') border-red-500 @enderror">{{ old('address', $supplier->address ?? '') }}</textarea>
    @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
</div>
