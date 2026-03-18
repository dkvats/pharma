@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-store"></i> All Stores
            </h1>
            <small class="text-muted">Manage all registered stores across the system</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.store-updates.index') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-pen-fancy"></i> Pending Updates
                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle p-1" 
                      id="pendingCount" style="font-size: 0.7em;">{{ number_format(App\Models\MR\StoreUpdateRequest::pending()->count()) }}</span>
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" name="status" id="status">
                        <option value="">All Statuses</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending Approval</option>
                        <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" id="search" 
                           placeholder="Store name, code..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Alerts -->
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stores Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Store Name</th>
                        <th>Store Code</th>
                        <th>Registered By (MR)</th>
                        <th>Location</th>
                        <th>Store Type</th>
                        <th>Status</th>
                        <th>Pending Updates</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stores as $store)
                        <tr>
                            <td>
                                <strong>{{ $store->store_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $store->owner_name }}</small>
                            </td>
                            <td>
                                <code>{{ $store->store_code }}</code>
                            </td>
                            <td>
                                {{ $store->mr?->name }}
                                <br>
                                <small class="text-muted">{{ $store->mr?->email }}</small>
                            </td>
                            <td>
                                {{ $store->city }}, {{ $store->state }}
                                <br>
                                <small class="text-muted">{{ $store->pincode }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $store->store_type ? ucfirst($store->store_type) : 'General' }}
                                </span>
                            </td>
                            <td>
                                @if ($store->isApproved())
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @elseif ($store->isPending())
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-hourglass-half"></i> Pending
                                    </span>
                                @elseif ($store->isRejected())
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i> Rejected
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-stop"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($store->pendingUpdateRequests->count() > 0)
                                    <span class="badge bg-warning text-dark">
                                        {{ $store->pendingUpdateRequests->count() }} pending
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">None</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('mr.stores.show', $store) }}" 
                                       class="btn btn-outline-primary" title="View Store">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('mr.stores.edit', $store) }}" 
                                       class="btn btn-outline-secondary" title="Edit Store">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($store->pendingUpdateRequests->count() > 0)
                                        <a href="{{ route('admin.store-updates.index') }}" 
                                           class="btn btn-outline-warning" title="View Pending Updates">
                                            <i class="fas fa-pen-fancy"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-store text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No stores found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($stores->count() > 0)
            <div class="card-footer bg-light">
                {{ $stores->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
