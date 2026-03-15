<?php

namespace App\Http\Controllers;

use App\Models\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoleRequestController extends Controller
{
    /**
     * Show the role request form.
     * ?role=Doctor|Store|MR pre-selects the role.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        // Users who already have a non-End-User role don't need to request
        if ($user->hasAnyRole(['Doctor', 'Store', 'MR', 'Admin', 'Super Admin'])) {
            return redirect()->route('dashboard')
                ->with('info', 'You already have a specialized role assigned.');
        }

        // Check if the user has a pending request for the same role
        $preselectedRole = $request->query('role');
        $existingRequest = null;

        if ($preselectedRole && in_array($preselectedRole, ['Doctor', 'Store', 'MR'])) {
            $existingRequest = RoleRequest::where('user_id', $user->id)
                ->where('requested_role', $preselectedRole)
                ->where('status', 'pending')
                ->first();
        }

        // All of the user's requests (for status display)
        $myRequests = RoleRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('role-requests.create', compact('preselectedRole', 'existingRequest', 'myRequests'));
    }

    /**
     * Store a new role request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Security: already has a specialized role
        if ($user->hasAnyRole(['Doctor', 'Store', 'MR', 'Admin', 'Super Admin'])) {
            return redirect()->route('dashboard')
                ->with('error', 'You already have a specialized role.');
        }

        $validated = $request->validate([
            'requested_role' => ['required', 'in:Doctor,Store,MR'],
            'message'        => ['nullable', 'string', 'max:1000'],
            'document'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        // Prevent duplicate pending requests for the same role
        $existing = RoleRequest::where('user_id', $user->id)
            ->where('requested_role', $validated['requested_role'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', "You already have a pending request for the {$validated['requested_role']} role. Please wait for admin review.");
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')
                ->store('role-requests/' . $user->id, 'public');
        }

        RoleRequest::create([
            'user_id'        => $user->id,
            'requested_role' => $validated['requested_role'],
            'status'         => 'pending',
            'message'        => $validated['message'] ?? null,
            'document_path'  => $documentPath,
        ]);

        return redirect()->route('role-requests.create')
            ->with('success', "Your request for the {$validated['requested_role']} role has been submitted. An admin will review it shortly.");
    }

    /**
     * Admin: list all role requests.
     */
    public function adminIndex(Request $request)
    {
        $query = RoleRequest::with(['user', 'reviewer'])->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('role')) {
            $query->where('requested_role', $request->role);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $requests   = $query->paginate(20)->withQueryString();
        $pendingCount  = RoleRequest::pending()->count();
        $approvedCount = RoleRequest::approved()->count();
        $rejectedCount = RoleRequest::rejected()->count();

        return view('admin.role-requests.index', compact('requests', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Admin: approve a role request.
     */
    public function approve(Request $request, RoleRequest $roleRequest)
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if (!$roleRequest->isPending()) {
            return redirect()->back()->with('error', 'This request has already been reviewed.');
        }

        // Assign the requested role (syncRoles replaces all existing roles)
        $roleRequest->user->syncRoles([$roleRequest->requested_role]);

        // Update the request record
        $roleRequest->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'admin_note'  => $validated['admin_note'] ?? null,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "{$roleRequest->user->name} has been approved as {$roleRequest->requested_role}.");
    }

    /**
     * Admin: reject a role request.
     */
    public function reject(Request $request, RoleRequest $roleRequest)
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if (!$roleRequest->isPending()) {
            return redirect()->back()->with('error', 'This request has already been reviewed.');
        }

        $roleRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'admin_note'  => $validated['admin_note'] ?? null,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "Request from {$roleRequest->user->name} has been rejected.");
    }
}
