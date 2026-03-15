@extends('layouts.app')

@section('title', 'Edit Visit Report')
@section('page-title', 'Edit Visit Report')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold">Edit Daily Call Report (DCR)</h3>
            <a href="{{ route('mr.visits.show', $visit) }}" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </a>
        </div>
        
        <form action="{{ route('mr.visits.update', $visit) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Doctor Selection -->
            <div>
                <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-1">Select Doctor *</label>
                <select id="doctor_id" name="doctor_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('doctor_id') border-red-500 @enderror">
                    <option value="">Choose Doctor...</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $visit->doctor_id) == $doctor->id ? 'selected' : '' }}>
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
                    <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date', $visit->visit_date?->format('Y-m-d')) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('visit_date') border-red-500 @enderror">
                    @error('visit_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="visit_time" class="block text-sm font-medium text-gray-700 mb-1">Visit Time</label>
                    <input type="time" id="visit_time" name="visit_time" value="{{ old('visit_time', $visit->visit_time?->format('H:i')) }}"
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
                    <option value="completed" {{ old('status', $visit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="planned" {{ old('status', $visit->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                    <option value="cancelled" {{ old('status', $visit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="rescheduled" {{ old('status', $visit->status) == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
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
                    placeholder="Enter products discussed during the visit...">{{ old('products_discussed', $visit->products_discussed) }}</textarea>
                @error('products_discussed')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks / Notes</label>
                <textarea id="remarks" name="remarks" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('remarks') border-red-500 @enderror"
                    placeholder="Enter visit remarks, feedback, or notes...">{{ old('remarks', $visit->remarks) }}</textarea>
                @error('remarks')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Next Visit Date -->
            <div>
                <label for="next_visit_date" class="block text-sm font-medium text-gray-700 mb-1">Next Visit Date</label>
                <input type="date" id="next_visit_date" name="next_visit_date" value="{{ old('next_visit_date', $visit->next_visit_date?->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('next_visit_date') border-red-500 @enderror">
                @error('next_visit_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Current Photo -->
            @if($visit->photo_path)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Photo</label>
                <div class="mt-2">
                    <img src="{{ Storage::url($visit->photo_path) }}" alt="Current Visit Photo" class="max-w-xs h-auto rounded-lg border">
                </div>
                <p class="text-gray-500 text-sm mt-2">Upload a new photo below to replace this one.</p>
            </div>
            @endif
            
            <!-- Photo Upload -->
            <div>
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">{{ $visit->photo_path ? 'Replace Photo' : 'Visit Photo' }} (Optional)</label>
                <input type="file" id="photo" name="photo" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('photo') border-red-500 @enderror">
                <p class="text-gray-500 text-sm mt-1">Upload a photo of the visit (max 2MB).</p>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-4 pt-4 border-t">
                <a href="{{ route('mr.visits.show', $visit) }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Visit Report
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
