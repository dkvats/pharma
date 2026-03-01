<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\Store;
use Illuminate\Http\Request;

class StoreApprovalController extends Controller
{
    /**
     * List pending stores for approval
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $stores = Store::with(['mr', 'user'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $counts = [
            'pending' => Store::where('status', 'pending')->count(),
            'approved' => Store::where('status', 'approved')->count(),
            'rejected' => Store::where('status', 'rejected')->count(),
            'inactive' => Store::where('status', 'inactive')->count(),
        ];

        return view('admin.stores.approval.index', compact('stores', 'counts', 'status'));
    }

    /**
     * Show store approval details
     */
    public function show(Store $store)
    {
        $store->load(['mr', 'user', 'approver']);

        return view('admin.stores.approval.show', compact('store'));
    }

    /**
     * Approve a store
     */
    public function approve(Request $request, Store $store)
    {
        if (!$store->isPending()) {
            return redirect()->back()->with('error', 'Store is not in pending status.');
        }

        // Update store status
        $store->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        // Activate user account
        if ($store->user) {
            $store->user->update([
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.stores.approval.index')
            ->with('success', 'Store approved successfully. Store can now login and receive orders.');
    }

    /**
     * Reject a store
     */
    public function reject(Request $request, Store $store)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if (!$store->isPending()) {
            return redirect()->back()->with('error', 'Store is not in pending status.');
        }

        // Update store status
        $store->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
        ]);

        // Keep user account inactive
        if ($store->user) {
            $store->user->update([
                'status' => 'inactive',
            ]);
        }

        return redirect()->route('admin.stores.approval.index')
            ->with('success', 'Store rejected. Reason recorded.');
    }

    /**
     * Deactivate an approved store
     */
    public function deactivate(Request $request, Store $store)
    {
        if (!$store->isApproved()) {
            return redirect()->back()->with('error', 'Only approved stores can be deactivated.');
        }

        $store->update([
            'status' => 'inactive',
        ]);

        // Deactivate user account
        if ($store->user) {
            $store->user->update([
                'status' => 'inactive',
            ]);
        }

        return redirect()->route('admin.stores.approval.index')
            ->with('success', 'Store deactivated successfully.');
    }

    /**
     * Reactivate an inactive store
     */
    public function reactivate(Request $request, Store $store)
    {
        if ($store->status !== 'inactive') {
            return redirect()->back()->with('error', 'Only inactive stores can be reactivated.');
        }

        $store->update([
            'status' => 'approved',
        ]);

        // Reactivate user account
        if ($store->user) {
            $store->user->update([
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.stores.approval.index')
            ->with('success', 'Store reactivated successfully.');
    }
}
