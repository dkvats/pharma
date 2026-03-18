@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-ban"></i> Rejected Updates
            </h1>
            <small class="text-muted">Audit trail of rejected store modification requests</small>
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

    <!-- Rejections Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Store Name</th>
                        <th>Requested By</th>
                        <th>Rejected By</th>
                        <th>Rejected On</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rejections as $rejection)
                        <tr>
                            <td>
                                <strong>{{ $rejection->store?->store_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $rejection->store?->store_code }}</small>
                            </td>
                            <td>
                                {{ $rejection->requester?->name }}
                                <br>
                                <small class="text-muted">{{ ucfirst($rejection->requested_role) }}</small>
                            </td>
                            <td>
                                {{ $rejection->approver?->name }}
                                <br>
                                <small class="text-muted">{{ $rejection->approver?->email }}</small>
                            </td>
                            <td>
                                {{ $rejection->approved_at->format('M d, Y H:i') }}
                                <br>
                                <small class="text-muted">{{ $rejection->approved_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <small>
                                    {{ Str::limit($rejection->rejection_reason, 50) }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-danger" 
                                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $rejection->id }}"
                                            title="View Rejection Reason">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                    <a href="{{ route('admin.store-updates.show', $rejection) }}" 
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>

                                <!-- Reason Modal -->
                                <div class="modal fade" id="reasonModal{{ $rejection->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger bg-opacity-10">
                                                <h5 class="modal-title">Rejection Reason</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-0">{{ $rejection->rejection_reason }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No rejected updates found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rejections->count() > 0)
            <div class="card-footer bg-light">
                {{ $rejections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
