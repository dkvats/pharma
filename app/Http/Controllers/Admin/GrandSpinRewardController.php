<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrandSpinReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GrandSpinRewardController extends Controller
{
    /**
     * Display a listing of grand spin rewards.
     */
    public function index()
    {
        $rewards = GrandSpinReward::latest()->paginate(10);
        return view('admin.grand-spin-rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new grand spin reward.
     */
    public function create()
    {
        return view('admin.grand-spin-rewards.create');
    }

    /**
     * Store a newly created grand spin reward.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:cash,product,voucher,other'],
            'value' => ['required', 'numeric', 'min:0'],
            'probability' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'force_equal_distribution' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'probability' => $validated['probability'],
            'stock' => $validated['stock'],
            'is_active' => $request->boolean('is_active', true),
            'force_equal_distribution' => $request->boolean('force_equal_distribution', false),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rewards', 'public');
        }

        GrandSpinReward::create($data);

        return redirect()->route('admin.grand-spin-rewards.index')
            ->with('success', 'Grand Spin Reward created successfully.');
    }

    /**
     * Show the form for editing the specified grand spin reward.
     */
    public function edit(GrandSpinReward $grandSpinReward)
    {
        return view('admin.grand-spin-rewards.edit', ['reward' => $grandSpinReward]);
    }

    /**
     * Update the specified grand spin reward.
     */
    public function update(Request $request, GrandSpinReward $grandSpinReward)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:cash,product,voucher,other'],
            'value' => ['required', 'numeric', 'min:0'],
            'probability' => ['required', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'force_equal_distribution' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'value' => $validated['value'],
            'probability' => $validated['probability'],
            'stock' => $validated['stock'],
            'is_active' => $request->boolean('is_active', true),
            'force_equal_distribution' => $request->boolean('force_equal_distribution', false),
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($grandSpinReward->image && Storage::disk('public')->exists($grandSpinReward->image)) {
                Storage::disk('public')->delete($grandSpinReward->image);
            }
            $data['image'] = $request->file('image')->store('rewards', 'public');
        }

        $grandSpinReward->update($data);

        return redirect()->route('admin.grand-spin-rewards.index')
            ->with('success', 'Grand Spin Reward updated successfully.');
    }

    /**
     * Remove the specified grand spin reward.
     */
    public function destroy(GrandSpinReward $grandSpinReward)
    {
        // Delete image if exists
        if ($grandSpinReward->image && Storage::disk('public')->exists($grandSpinReward->image)) {
            Storage::disk('public')->delete($grandSpinReward->image);
        }

        $grandSpinReward->delete();

        return redirect()->route('admin.grand-spin-rewards.index')
            ->with('success', 'Grand Spin Reward deleted successfully.');
    }

    /**
     * Toggle reward status.
     */
    public function toggleStatus(GrandSpinReward $grandSpinReward)
    {
        $grandSpinReward->is_active = !$grandSpinReward->is_active;
        $grandSpinReward->save();

        return back()->with('success', 'Grand Spin Reward status updated.');
    }
}
