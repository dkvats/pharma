@extends('layouts.app')

@section('title', 'Add Visit Report')
@section('page-title', 'Add Visit Report')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Daily Call Report (DCR)</h3>
        </div>
        
        <form action="{{ route('mr.visits.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
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
            
            <!-- Visit Date & Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="visit_date" class="block text-sm font-medium text-gray-700 mb-1">Visit Date *</label>
                    <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', today()->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('visit_date') border-red-500 @enderror">
                    @error('visit_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="visit_time" class="block text-sm font-medium text-gray-700 mb-1">Visit Time</label>
                    <input type="time" id="visit_time" name="visit_time" value="{{ old('visit_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('visit_time') border-red-500 @enderror">
                    @error('visit_time')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Visit Status *</label>
                <select id="status" name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rescheduled" {{ old('status') == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Products Discussed -->
            <div>
                <label for="products_discussed" class="block text-sm font-medium text-gray-700 mb-1">Products Discussed</label>
                <textarea id="products_discussed" name="products_discussed" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('products_discussed') border-red-500 @enderror"
                    placeholder="Enter products discussed during the visit...">{{ old('products_discussed') }}</textarea>
                @error('products_discussed')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Promoted Products (structured) -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <label class="block text-sm font-semibold text-indigo-800 mb-2">Promoted Products</label>
                <p class="text-xs text-indigo-600 mb-3">Select products you actively promoted to this doctor during the visit.</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                    @foreach($products as $product)
                    <label class="flex items-center gap-2 text-sm p-2 rounded hover:bg-indigo-100 cursor-pointer">
                        <input type="checkbox" name="promoted_products[]" value="{{ $product->id }}"
                            {{ in_array($product->id, old('promoted_products', [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600">
                        <span class="text-gray-800">{{ $product->name }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-3">
                    <label for="promotion_notes" class="block text-xs font-medium text-indigo-700 mb-1">Promotion Notes</label>
                    <textarea id="promotion_notes" name="promotion_notes" rows="2"
                        class="w-full px-3 py-1.5 text-sm border border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        placeholder="e.g., Doctor interested in new formulation...">{{ old('promotion_notes') }}</textarea>
                </div>
            </div>
            
            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks / Notes</label>
                <textarea id="remarks" name="remarks" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('remarks') border-red-500 @enderror"
                    placeholder="Enter visit remarks, feedback, or notes...">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Next Visit Date -->
            <div>
                <label for="next_visit_date" class="block text-sm font-medium text-gray-700 mb-1">Next Visit Date</label>
                <input type="date" id="next_visit_date" name="next_visit_date" value="{{ old('next_visit_date') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('next_visit_date') border-red-500 @enderror">
                @error('next_visit_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Photo Upload -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Visit Photo (Optional)</label>
                <input type="file" id="photo" name="photo" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('photo') border-red-500 @enderror">
                <p class="text-gray-500 text-sm mt-1">Upload a photo of the visit (max 2MB).</p>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('mr.visits.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Visit Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
