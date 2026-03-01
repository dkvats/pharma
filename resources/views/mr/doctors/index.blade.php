@extends('layouts.app')

@section('title', 'My Doctors')
@section('page-title', 'Doctor Management')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold">My Doctors</h2>
        <a href="{{ route('mr.doctors.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Register New Doctor
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specialization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $doctor->doctor_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $doctor->name }}</div>
                                <div class="text-sm text-gray-500">{{ $doctor->clinic_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $doctor->specialization ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $doctor->area?->name }}, {{ $doctor->city?->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $doctor->mobile ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                {!! $doctor->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <div class="flex items-center gap-2 justify-center">
                                    {{-- View Button - Always visible and clickable --}}
                                    <a href="{{ route('mr.doctors.show', $doctor->id) }}" 
                                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm inline-flex items-center" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Edit Button - Always visible, conditionally clickable --}}
                                    @if($doctor->status == 'pending' || $doctor->status == 'rejected')
                                        <a href="{{ route('mr.doctors.edit', $doctor->id) }}" 
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm inline-flex items-center" 
                                           title="Edit Doctor">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @else
                                        <span class="bg-gray-300 text-gray-600 px-3 py-1 rounded text-sm inline-flex items-center cursor-not-allowed" 
                                              title="Cannot edit ({{ $doctor->status }})">
                                            <i class="fas fa-edit"></i>
                                        </span>
                                    @endif
                                    
                                    {{-- Status Indicator - Always visible --}}
                                    @if($doctor->status == 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold" title="Pending Approval">⏳</span>
                                    @elseif($doctor->status == 'approved')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold" title="Approved">✓</span>
                                    @elseif($doctor->status == 'rejected')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold" title="Rejected">✗</span>
                                    @elseif($doctor->status == 'inactive')
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold" title="Inactive">⏸</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs font-semibold" title="Unknown">?</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No doctors found. <a href="{{ route('mr.doctors.create') }}" class="text-blue-600 hover:underline">Register a doctor</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $doctors->links() }}
        </div>
    </div>
</div>
@endsection
