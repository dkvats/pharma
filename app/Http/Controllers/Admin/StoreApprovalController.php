<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\Store;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class StoreApprovalController extends Controller
{
    public function pending(Request $request)
    {
        $request->merge(['status' => 'pending']);
        return $this->index($request);
    }

    /**
     * List pending stores for approval
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $stores = Store::with(['mr', 'assignedMr', 'user'])
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

        $mrs = \App\Models\User::role('MR')->orderBy('name')->get(['id', 'name']);

        return view('admin.stores.approval.index', compact('stores', 'counts', 'status', 'mrs'));
    }

    /**
     * Show store approval details
     */
    public function show(Store $store)
    {
        $store->load(['mr', 'assignedMr', 'user', 'approver']);

        $mrs = \App\Models\User::role('MR')->orderBy('name')->get(['id', 'name']);

        return view('admin.stores.approval.show', compact('store', 'mrs'));
    }

    /**
     * Approve a store
     */
    public function approve(Request $request, Store $store)
    {
        $validated = $request->validate([
            'mr_id' => ['required', 'exists:users,id'],
        ]);

        $mr = \App\Models\User::role('MR')->find($validated['mr_id']);
        if (!$mr) {
            return back()->with('error', 'Please select a valid MR for assignment.');
        }

        if (!$store->isPending()) {
            return redirect()->back()->with('error', 'Store is not in pending status.');
        }

        // Update store status
        $store->update([
            'mr_id' => $mr->id,
            'assigned_mr_id' => $mr->id,
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);

        // Activate user account
        if ($store->user) {
            $store->user->update([
                'status' => 'approved',
                'role' => 'store',
            ]);
            
            // Send approval notification
            NotificationService::sendStoreApproval($store->user, 'approved');
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
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if (!$store->isPending()) {
            return redirect()->back()->with('error', 'Store is not in pending status.');
        }

        // Update store status
        $store->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason ?: 'Rejected by admin.',
            'approved_by' => auth()->id(),
        ]);

        // Keep user account inactive
        if ($store->user) {
            $store->user->update([
                'status' => 'rejected',
                'role' => 'store',
            ]);
            
            // Send rejection notification
            NotificationService::sendStoreApproval($store->user, 'rejected', $request->rejection_reason ?: 'Rejected by admin.');
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
                'status' => 'approved',
            ]);
        }

        return redirect()->route('admin.stores.approval.index')
            ->with('success', 'Store reactivated successfully.');
    }

    /**
     * Find the MR responsible for a given area.
     * 
     * Logic: Select MR with the highest number of doctors in this area.
     * Falls back to any MR with matching assigned_area string if no doctors found.
     * 
     * STEP 4 — Safety: Returns null if no MR found, allowing manual assignment later.
     */
    private function findMRForArea(int $areaId): ?\App\Models\User
    {
        // STEP 2: Find MR with highest number of doctors in this area
        $topMR = Doctor::where('area_id', $areaId)
            ->whereNotNull('created_by')
            ->select('created_by')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('created_by')
            ->orderByDesc('total')
            ->first();

        if ($topMR) {
            $mr = \App\Models\User::role('MR')->find($topMR->created_by);
            if ($mr) {
                return $mr;
            }
        }

        // STEP 4: Fallback — try to match by area name in assigned_area field
        $area = \App\Models\MR\Area::find($areaId);
        if ($area) {
            $mrFromAreaName = \App\Models\User::where('assigned_area', 'like', '%' . $area->name . '%')
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'MR');
                })
                ->first();

            if ($mrFromAreaName) {
                return $mrFromAreaName;
            }
        }

        // No MR found — return null (Admin can assign manually later)
        return null;
    }
}
