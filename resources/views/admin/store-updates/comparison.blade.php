@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-code-compare"></i> Compare Changes
            </h1>
            <small class="text-muted">Current vs Proposed Values</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.store-updates.show', $updateRequest) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Review
            </a>
        </div>
    </div>

    @php
        $changes = $updateRequest->getChangesSummary();
    @endphp

    <!-- Store Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h5>{{ $store->store_name }}</h5>
                    <p class="text-muted mb-0">
                        <span class="badge bg-secondary">{{ $store->store_code }}</span>
                        <span class="badge bg-info">{{ $store->store_type ?? 'N/A' }}</span>
                    </p>
                </div>
                <div class="col text-end">
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-hourglass-half"></i> Pending Approval
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Table -->
    @if (count($changes) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 25%;">Field Name</th>
                            <th style="width: 37.5%;">
                                <span class="badge bg-danger">CURRENT</span>
                            </th>
                            <th style="width: 37.5%;">
                                <span class="badge bg-success">PROPOSED</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($changes as $field => $change)
                            <tr class="border-bottom">
                                <td class="py-3">
                                    <strong>{{ $change['label'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $field }}</small>
                                </td>
                                <td class="py-3 bg-danger bg-opacity-10">
                                    <code class="text-danger">
                                        {{ $change['old_value'] ?? '(empty)' }}
                                    </code>
                                </td>
                                <td class="py-3 bg-success bg-opacity-10">
                                    <code class="text-success">
                                        {{ $change['new_value'] ?? '(empty)' }}
                                    </code>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-light">
                <small class="text-muted">
                    Total: <strong>{{ count($changes) }}</strong> field{{ count($changes) !== 1 ? 's' : '' }} changed
                </small>
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i> No changes detected in this update request.
        </div>
    @endif

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Request Details</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Requested By:</dt>
                        <dd class="col-sm-6"><strong>{{ $updateRequest->requester?->name }}</strong></dd>
                        
                        <dt class="col-sm-6">Role:</dt>
                        <dd class="col-sm-6"><strong>{{ ucfirst($updateRequest->requested_role) }}</strong></dd>

                        <dt class="col-sm-6">Requested:</dt>
                        <dd class="col-sm-6">{{ $updateRequest->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Store Details</h6>
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Code:</dt>
                        <dd class="col-sm-6"><strong>{{ $store->store_code }}</strong></dd>
                        
                        <dt class="col-sm-6">Registered:</dt>
                        <dd class="col-sm-6">{{ $store->created_at->format('M d, Y') }}</dd>

                        <dt class="col-sm-6">Status:</dt>
                        <dd class="col-sm-6">
                            @if ($store->isApproved())
                                <span class="badge bg-success">Active</span>
                            @elseif ($store->isPending())
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 bg-secondary bg-opacity-10">
                <div class="card-body">
                    <h6 class="card-title text-muted mb-2">Actions</h6>
                    <a href="{{ route('admin.store-updates.show', $updateRequest) }}" 
                       class="btn btn-sm btn-primary w-100 mb-2">
                        <i class="fas fa-check-circle"></i> Approve/Reject
                    </a>
                    <a href="{{ route('mr.stores.show', $store) }}" 
                       class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-eye"></i> View Store
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
