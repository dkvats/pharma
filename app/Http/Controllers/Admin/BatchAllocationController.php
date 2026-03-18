<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBatch;
use App\Models\StoreInventory;
use App\Models\User;
use Illuminate\Http\Request;

class BatchAllocationController extends Controller
{
    /**
     * Allocate a quantity from a batch to a store.
     * Deducts from the central batch quantity.
     */
    public function store(Request $request, ProductBatch $batch)
    {
        $validated = $request->validate([
            'store_id' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        // Ensure we don't over-allocate beyond available batch quantity
        $alreadyAllocated = $batch->storeInventories()->sum('quantity');
        $available        = $batch->quantity;

        if ($validated['quantity'] > $available) {
            return back()->withErrors([
                'quantity' => "Cannot allocate {$validated['quantity']} units. Only {$available} available in this batch.",
            ]);
        }

        // Upsert: increment if the store already has an allocation for this batch
        $existing = StoreInventory::where('store_id', $validated['store_id'])
            ->where('product_batch_id', $batch->id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $validated['quantity'];
            if ($newQty > $available) {
                return back()->withErrors([
                    'quantity' => "Total allocation for this store would exceed available batch quantity ({$available}).",
                ]);
            }
            $existing->increment('quantity', $validated['quantity']);
        } else {
            StoreInventory::create([
                'store_id'         => $validated['store_id'],
                'product_batch_id' => $batch->id,
                'quantity'         => $validated['quantity'],
            ]);
        }

        $store = User::find($validated['store_id']);
        return back()->with('success', "Allocated {$validated['quantity']} units to {$store->name}.");
    }

    /**
     * Remove a store allocation record.
     */
    public function destroy(StoreInventory $allocation)
    {
        $qty   = $allocation->quantity;
        $store = $allocation->store->name ?? 'Store';
        $allocation->delete();

        return back()->with('success', "Removed allocation of {$qty} units from {$store}.");
    }
}
