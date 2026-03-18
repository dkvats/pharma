@extends('layouts.app')

@section('title', 'Doctor Approval Details')
@section('page-title', 'Doctor Approval Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Doctor Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div><span class="text-gray-500">Name:</span> <span class="font-medium">{{ $doctor->name }}</span></div>
            <div><span class="text-gray-500">Code:</span> <span class="font-medium">{{ $doctor->doctor_code }}</span></div>
            <div><span class="text-gray-500">Mobile:</span> <span class="font-medium">{{ $doctor->mobile ?: 'N/A' }}</span></div>
            <div><span class="text-gray-500">Email:</span> <span class="font-medium">{{ $doctor->email ?: 'N/A' }}</span></div>
            <div><span class="text-gray-500">Clinic:</span> <span class="font-medium">{{ $doctor->clinic_name ?: 'N/A' }}</span></div>
            <div><span class="text-gray-500">License:</span> <span class="font-medium">{{ $doctor->license_no ?: 'N/A' }}</span></div>
            <div><span class="text-gray-500">Pincode:</span> <span class="font-medium">{{ $doctor->pincode ?: 'N/A' }}</span></div>
            <div><span class="text-gray-500">City:</span> <span class="font-medium">{{ $doctor->city->name ?? $doctor->city ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">District:</span> <span class="font-medium">{{ $doctor->district->name ?? $doctor->district ?? 'N/A' }}</span></div>
            <div><span class="text-gray-500">State:</span> <span class="font-medium">{{ $doctor->state->name ?? $doctor->state ?? 'N/A' }}</span></div>
            <div class="md:col-span-2"><span class="text-gray-500">Address:</span> <span class="font-medium">{{ $doctor->address ?: 'N/A' }}</span></div>
        </div>
    </div>

    @if($doctor->isPending())
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Approval Action</h3>
            <div class="flex flex-col md:flex-row gap-3">
                <form method="POST" action="{{ route('admin.doctors.approval.approve', $doctor) }}" class="flex items-center gap-2">
                    @csrf
                    <select name="mr_id" required class="border-gray-300 rounded-md">
                        <option value="">Assign MR</option>
                        @foreach($mrs as $mr)
                            <option value="{{ $mr->id }}">{{ $mr->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Approve</button>
                </form>

                <form method="POST" action="{{ route('admin.doctors.approval.reject', $doctor) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="border-gray-300 rounded-md">
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Reject</button>
                </form>
            </div>
        </div>
    @endif

    <div>
        <a href="{{ route('admin.doctors.approval.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">&larr; Back to list</a>
    </div>
</div>
@endsection
