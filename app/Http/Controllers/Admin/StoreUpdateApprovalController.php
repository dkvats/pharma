<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\Store;
use App\Models\MR\StoreUpdateRequest;
use App\Models\User;
use App\Notifications\StoreUpdateApproved;
use App\Notifications\StoreUpdateRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreUpdateApprovalController extends Controller
{
    /**
     * Display all pending store update requests for approval
     */
    public function index()
    {
        $this->authorize('view admin approval page');

        $updates = StoreUpdateRequest::with([
            'store',
            'store.mr',
            'requester',
            'approver',
        ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'pending' => StoreUpdateRequest::pending()->count(),
            'approved' => StoreUpdateRequest::approved()->count(),
            'rejected' => StoreUpdateRequest::rejected()->count(),
        ];

        return view('admin.store-updates.index', compact('updates', 'stats'));
    }

    /**
     * View update request details with changes summary
     */
    public function show(StoreUpdateRequest $updateRequest)
    {
        $this->authorize('view admin approval page');

        $updateRequest->load('store', 'store.mr', 'requester');

        // Get changes summary
        $changes = $updateRequest->getChangesSummary();

        return view('admin.store-updates.show', compact('updateRequest', 'changes'));
    }

    /**
     * Approve a store update request
     */
    public function approve(Request $request, StoreUpdateRequest $updateRequest)
    {
        $this->authorize('approve store update requests');

        if (!$updateRequest->isPending()) {
            return back()->with('error', 'Can only approve pending requests.');
        }

        try {
            DB::transaction(function () use ($updateRequest) {
                // Apply changes to store
                $updateRequest->applyToStore();

                // Update the request status
                $updateRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                // Log activity
                activity()
                    ->performedOn($updateRequest->store)
                    ->causedBy(auth()->user())
                    ->log('Store update request approved by: ' . auth()->user()->name);
            });

            // Notification: Notify MR that update was approved
            // $updateRequest->requester->notify(new StoreUpdateApproved($updateRequest));

            return redirect()->route('admin.store-updates.index')
                ->with('success', 'Store update request approved successfully. Changes applied to store.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve update: ' . $e->getMessage());
        }
    }

    /**
     * Reject a store update request
     */
    public function reject(Request $request, StoreUpdateRequest $updateRequest)
    {
        $this->authorize('reject store update requests');

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        if (!$updateRequest->isPending()) {
            return back()->with('error', 'Can only reject pending requests.');
        }

        try {
            $updateRequest->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);

            // Log activity
            activity()
                ->performedOn($updateRequest->store)
                ->causedBy(auth()->user())
                ->log('Store update request rejected by: ' . auth()->user()->name . '. Reason: ' . $request->input('rejection_reason'));

            // Notification: Notify MR that update was rejected
            $updateRequest->requester->notify(new StoreUpdateRejected($updateRequest));

            return redirect()->route('admin.store-updates.index')
                ->with('success', 'Store update request rejected successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject update: ' . $e->getMessage());
        }
    }

    /**
     * View rejected update requests
     */
    public function rejectedHistory()
    {
        $this->authorize('view admin approval page');

        $rejections = StoreUpdateRequest::with([
            'store',
            'store.mr',
            'requester',
            'approver',
        ])
            ->where('status', 'rejected')
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('admin.store-updates.rejected', compact('rejections'));
    }

    /**
     * View approved update requests (audit trail)
     */
    public function approvedHistory()
    {
        $this->authorize('view admin approval page');

        $approvals = StoreUpdateRequest::with([
            'store',
            'store.mr',
            'requester',
            'approver',
        ])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('admin.store-updates.approved', compact('approvals'));
    }

    /**
     * Get update request comparison view
     */
    public function comparison(StoreUpdateRequest $updateRequest)
    {
        $this->authorize('view admin approval page');

        $store = $updateRequest->store;
        $changes = $updateRequest->getChangesSummary();

        return view('admin.store-updates.comparison', compact('updateRequest', 'store', 'changes'));
    }
}
