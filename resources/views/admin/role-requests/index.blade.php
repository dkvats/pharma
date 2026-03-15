@extends('layouts.enterprise')

@section('title', 'Role Requests')
@section('page-title', 'Role Requests')
@section('page-description', 'Review and manage user requests for Doctor, Store, and MR roles')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
        <i class="fas fa-check-circle text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-yellow-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                <p class="text-sm text-gray-500">Pending</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $approvedCount }}</p>
                <p class="text-sm text-gray-500">Approved</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                <p class="text-sm text-gray-500">Rejected</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.role-requests.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search User</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or email..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Roles</option>
                    <option value="Doctor" {{ request('role') === 'Doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="Store"  {{ request('role') === 'Store'  ? 'selected' : '' }}>Store</option>
                    <option value="MR"     {{ request('role') === 'MR'     ? 'selected' : '' }}>MR</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'role']))
                <a href="{{ route('admin.role-requests.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">
                Role Requests
                <span class="ml-2 text-sm font-normal text-gray-400">({{ $requests->total() }} total)</span>
            </h2>
        </div>

        @if($requests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Requested Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Document</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($requests as $roleRequest)
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- User --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-gray-600">{{ strtoupper(substr($roleRequest->user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $roleRequest->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $roleRequest->user->email }}</p>
                                    <p class="text-xs text-gray-400">Current: {{ $roleRequest->user->getRoleNames()->join(', ') ?: 'End User' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs
                                    @if($roleRequest->requested_role === 'Doctor') bg-blue-100 text-blue-600
                                    @elseif($roleRequest->requested_role === 'Store') bg-purple-100 text-purple-600
                                    @else bg-orange-100 text-orange-600 @endif">
                                    <i class="fas {{ $roleRequest->role_icon }}"></i>
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ $roleRequest->requested_role }}</span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleRequest->status_badge_class }}">
                                {{ ucfirst($roleRequest->status) }}
                            </span>
                            @if($roleRequest->reviewed_at)
                            <p class="text-xs text-gray-400 mt-1">{{ $roleRequest->reviewed_at->format('d M Y') }}</p>
                            @endif
                        </td>

                        {{-- Message --}}
                        <td class="px-6 py-4 max-w-xs">
                            @if($roleRequest->message)
                                <p class="text-sm text-gray-600 truncate" title="{{ $roleRequest->message }}">
                                    {{ Str::limit($roleRequest->message, 60) }}
                                </p>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                            @if($roleRequest->admin_note)
                                <p class="text-xs text-indigo-600 mt-1 truncate italic" title="{{ $roleRequest->admin_note }}">
                                    Note: {{ Str::limit($roleRequest->admin_note, 40) }}
                                </p>
                            @endif
                        </td>

                        {{-- Document --}}
                        <td class="px-6 py-4">
                            @if($roleRequest->document_path)
                                <a href="{{ Storage::url($roleRequest->document_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-file-alt"></i> View
                                </a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Submitted --}}
                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $roleRequest->created_at->format('d M Y') }}
                            <br>
                            <span class="text-xs text-gray-400">{{ $roleRequest->created_at->diffForHumans() }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 text-center">
                            @if($roleRequest->isPending())
                            <div class="flex items-center justify-center gap-2">
                                {{-- Approve --}}
                                <form action="{{ route('admin.role-requests.approve', $roleRequest) }}" method="POST"
                                      onsubmit="return confirmAction(this, 'Approve', '{{ $roleRequest->user->name }}', '{{ $roleRequest->requested_role }}')">
                                    @csrf
                                    <input type="hidden" name="admin_note" class="approve-note-{{ $roleRequest->id }}" value="">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form action="{{ route('admin.role-requests.reject', $roleRequest) }}" method="POST"
                                      onsubmit="return confirmAction(this, 'Reject', '{{ $roleRequest->user->name }}', '{{ $roleRequest->requested_role }}')">
                                    @csrf
                                    <input type="hidden" name="admin_note" class="reject-note-{{ $roleRequest->id }}" value="">
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-lg hover:bg-red-200 transition-colors">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="text-xs text-gray-400">
                                @if($roleRequest->reviewer)
                                    Reviewed by<br>
                                    <span class="font-medium text-gray-600">{{ $roleRequest->reviewer->name }}</span>
                                @else
                                    Reviewed
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
        @endif

        @else
        <div class="py-16 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 font-medium">No role requests found</p>
            <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
        </div>
        @endif
    </div>

</div>

{{-- Approve/Reject with optional note modal via prompt --}}
<script>
function confirmAction(form, action, userName, role) {
    const note = prompt(
        action + ' "' + userName + '" as ' + role + '?\n\nAdd an optional note for the user (leave blank to skip):',
        ''
    );
    if (note === null) return false; // cancelled

    // Set note value in the hidden input
    const noteInput = form.querySelector('input[name="admin_note"]');
    if (noteInput) noteInput.value = note;
    return true;
}
</script>
@endsection
