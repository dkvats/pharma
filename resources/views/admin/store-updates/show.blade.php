@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-pen-fancy"></i> Review Update Request
            </h1>
            <small class="text-muted">{{ $updateRequest->store?->store_name }} - {{ $updateRequest->store?->store_code }}</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.store-updates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @php
        $changes = $updateRequest->getChangesSummary();
    @endphp

    <!-- Request Info -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Request Information</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Requested By:</td>
                            <td><strong>{{ $updateRequest->requester?->name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email:</td>
                            <td>{{ $updateRequest->requester?->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Requested On:</td>
                            <td>{{ $updateRequest->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Store Information</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Store Name:</td>
                            <td><strong>{{ $updateRequest->store?->store_name }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Store Code:</td>
                            <td>{{ $updateRequest->store?->store_code }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Registered By:</td>
                            <td>{{ $updateRequest->store?->mr?->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Changes:</td>
                            <td>
                                <span class="badge bg-info">{{ count($changes) }} field{{ count($changes) !== 1 ? 's' : '' }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Changes Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Changes Summary
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Field</th>
                        <th>Current Value</th>
                        <th>Proposed Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($changes as $field => $change)
                        <tr>
                            <td>
                                <strong>{{ $change['label'] }}</strong>
                            </td>
                            <td>
                                <span class="text-danger">
                                    {{ $change['old_value'] ?? '(empty)' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-success">
                                    {{ $change['new_value'] ?? '(empty)' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                No changes detected
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approval Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <!-- Approve Button -->
            <div class="card border-success border-2 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-check-circle"></i> Approve Request
                    </h5>
                    <p class="card-text text-muted mb-3">
                        Click below to approve these changes. All modifications will be applied to the store immediately.
                    </p>

                    <form action="{{ route('admin.store-updates.approve', $updateRequest) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to approve this update request?');">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check"></i> Approve Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Reject Option -->
            <div class="card border-danger border-2 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-danger">
                        <i class="fas fa-times-circle"></i> Reject Request
                    </h5>
                    <p class="card-text text-muted mb-3">
                        Provide a reason for rejection. The MR will be notified and can resubmit.
                    </p>

                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="collapse"
                            data-bs-target="#rejectForm">
                        <i class="fas fa-times"></i> Reject & Provide Reason
                    </button>

                    <div class="collapse mt-3" id="rejectForm">
                        <form action="{{ route('admin.store-updates.reject', $updateRequest) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                <textarea class="form-control @error('rejection_reason') is-invalid @enderror" 
                                          id="rejection_reason" name="rejection_reason" rows="4" 
                                          placeholder="Explain why this update is being rejected..."></textarea>
                                @error('rejection_reason')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times"></i> Reject Update Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Original Store -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-store"></i> Store Details
            </h5>
        </div>
        <div class="card-body">
            <a href="{{ route('mr.stores.show', $updateRequest->store) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-external-link"></i> View Complete Store Profile
            </a>
        </div>
    </div>
</div>
@endsection
