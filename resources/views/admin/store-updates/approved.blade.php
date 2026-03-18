@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-history"></i> Approved Updates
            </h1>
            <small class="text-muted">Audit trail of approved store modifications</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.store-updates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Pending Requests
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Approvals Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Store Name</th>
                        <th>Requested By</th>
                        <th>Approved By</th>
                        <th>Approved On</th>
                        <th>Changes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($approvals as $approval)
                        <tr>
                            <td>
                                <strong>{{ $approval->store?->store_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $approval->store?->store_code }}</small>
                            </td>
                            <td>
                                {{ $approval->requester?->name }}
                                <br>
                                <small class="text-muted">{{ ucfirst($approval->requested_role) }}</small>
                            </td>
                            <td>
                                {{ $approval->approver?->name }}
                                <br>
                                <small class="text-muted">{{ $approval->approver?->email }}</small>
                            </td>
                            <td>
                                {{ $approval->approved_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $approval->approved_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @php
                                    $changes = $approval->getChangesSummary();
                                    $count = count($changes);
                                @endphp
                                <span class="badge bg-success">{{ $count }} field{{ $count !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.store-updates.show', $approval) }}" 
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.store-updates.comparison', $approval) }}" 
                                       class="btn btn-outline-info" title="Compare Changes">
                                        <i class="fas fa-code-compare"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-check-circle text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No approved updates found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($approvals->count() > 0)
            <div class="card-footer bg-light">
                {{ $approvals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
