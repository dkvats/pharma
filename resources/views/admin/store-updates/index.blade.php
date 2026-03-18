@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-pen-fancy"></i> Store Update Requests
            </h1>
            <small class="text-muted">Approve or reject MR store modification requests</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-store"></i> All Stores
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning bg-opacity-10 border-warning">
                <div class="card-body">
                    <h6 class="card-title text-warning">Pending</h6>
                    <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success bg-opacity-10 border-success">
                <div class="card-body">
                    <h6 class="card-title text-success">Approved</h6>
                    <h3 class="mb-0">{{ $stats['approved'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger bg-opacity-10 border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger">Rejected</h6>
                    <h3 class="mb-0">{{ $stats['rejected'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.store-updates.approved') }}" class="card border-2 border-success text-decoration-none">
                <div class="card-body">
                    <h6 class="card-title">View Approved</h6>
                    <i class="fas fa-arrow-right text-success"></i>
                </div>
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

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Updates Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Store Name</th>
                        <th>Requested By</th>
                        <th>Requested On</th>
                        <th>Fields Changed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($updates as $update)
                        <tr>
                            <td>
                                <strong>{{ $update->store?->store_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $update->store?->store_code }}</small>
                            </td>
                            <td>
                                {{ $update->requester?->name }}
                                <br>
                                <small class="text-muted">{{ $update->requester?->email }}</small>
                            </td>
                            <td>
                                {{ $update->created_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $update->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @php
                                    $changes = $update->getChangesSummary();
                                    $count = count($changes);
                                @endphp
                                <span class="badge bg-info">{{ $count }} field{{ $count !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.store-updates.show', $update) }}" 
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.store-updates.comparison', $update) }}" 
                                       class="btn btn-outline-info" title="Compare Changes">
                                        <i class="fas fa-code-compare"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No pending update requests</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($updates->count() > 0)
            <div class="card-footer bg-light">
                {{ $updates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
