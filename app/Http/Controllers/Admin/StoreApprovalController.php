<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\MR\Store;
use App\Services\NotificationService;
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

        // STEP 2 — Auto-assign MR based on store's area if mr_id is not set
        if (empty($store->mr_id) && !empty($store->area_id)) {
            $mr = $this->findMRForArea($store->area_id);

            // Fallback: assign first available MR if none found for area
            if (!$mr) {
                $mr = \App\Models\User::role('MR')->first();
            }

            if ($mr) {
                $store->mr_id = $mr->id;
            }
            // If no MR found at all, mr_id remains NULL (Admin can assign manually later)
        }

        // Update store status
        $store->update([
            'mr_id' => $store->mr_id,
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
            
            // Send rejection notification
            NotificationService::sendStoreApproval($store->user, 'rejected', $request->rejection_reason);
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
