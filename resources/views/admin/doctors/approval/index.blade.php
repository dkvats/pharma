@extends('layouts.app')

@section('title', 'Doctor Approval')
@section('page-title', 'Doctor Approval Requests')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Doctor Approval Requests</h2>
            <p class="text-gray-600 mt-1">Review and manage MR-submitted doctor registrations</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.doctors.approval.index', ['status' => 'pending']) }}" 
           class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 {{ $status === 'pending' ? 'ring-2 ring-yellow-400' : '' }}">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.doctors.approval.index', ['status' => 'approved']) }}" 
           class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 {{ $status === 'approved' ? 'ring-2 ring-green-400' : '' }}">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Approved</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.doctors.approval.index', ['status' => 'rejected']) }}" 
           class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 {{ $status === 'rejected' ? 'ring-2 ring-red-400' : '' }}">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Rejected</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                </div>
            </div>
        </a>

        <a href="{{ route('admin.doctors.approval.index', ['status' => 'inactive']) }}" 
           class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 {{ $status === 'inactive' ? 'ring-2 ring-gray-400' : '' }}">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause text-gray-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-500">Inactive</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Doctors Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned MR</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user-md text-gray-500"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $doctor->doctor_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $doctor->specialization ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $doctor->city->name ?? $doctor->city ?? '' }}</div>
                                <div class="text-sm text-gray-500">{{ $doctor->district->name ?? $doctor->district ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $doctor->assignedMr->name ?? 'Not assigned' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($doctor->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($doctor->status === 'approved') bg-green-100 text-green-800
                                    @elseif($doctor->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($doctor->status === 'inactive') bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($doctor->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    @if($doctor->status === 'pending')
                                        <form method="POST" action="{{ route('admin.doctors.approval.approve', $doctor->id) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            <select name="mr_id" required class="text-sm border-gray-300 rounded-md">
                                                <option value="">Assign MR</option>
                                                @foreach($mrs as $mr)
                                                    <option value="{{ $mr->id }}">{{ $mr->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600" onclick="return confirm('Approve this doctor?')">
                                                <i class="fas fa-check mr-1"></i>Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.doctors.approval.reject', $doctor->id) }}" class="inline-flex items-center gap-2">
                                            @csrf
                                            <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="text-sm border-gray-300 rounded-md">
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600" onclick="return confirm('Reject this doctor?')">
                                                <i class="fas fa-times mr-1"></i>Reject
                                            </button>
                                        </form>
                                    @elseif($doctor->status === 'approved')
                                        <form method="POST" action="{{ route('admin.doctors.approval.deactivate', $doctor->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                                                <i class="fas fa-pause mr-1"></i>Deactivate
                                            </button>
                                        </form>
                                    @elseif($doctor->status === 'inactive')
                                        <form method="POST" action="{{ route('admin.doctors.approval.reactivate', $doctor->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                                                <i class="fas fa-play mr-1"></i>Reactivate
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.doctors.approval.show', $doctor->id) }}" class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900">No {{ $status }} doctors</p>
                                    <p class="text-sm text-gray-500 mt-1">All caught up!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($doctors->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $doctors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
