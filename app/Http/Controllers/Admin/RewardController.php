<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardController extends Controller
{
    /**
     * Display a listing of rewards.
     */
    public function index()
    {
        $rewards = Reward::latest()->paginate(10);
        return view('admin.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new reward.
     */
    public function create()
    {
        return view('admin.rewards.create');
    }

    /**
     * Store a newly created reward.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:cash,product,gift_card,other'],
            'value' => ['required', 'numeric', 'min:0'],
            'probability' => ['required', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
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
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rewards', 'public');
        }

        Reward::create($data);

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Reward created successfully.');
    }

    /**
     * Show the form for editing the specified reward.
     */
    public function edit(Reward $reward)
    {
        return view('admin.rewards.edit', compact('reward'));
    }

    /**
     * Update the specified reward.
     */
    public function update(Request $request, Reward $reward)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:cash,product,gift_card,other'],
            'value' => ['required', 'numeric', 'min:0'],
            'probability' => ['required', 'numeric', 'min:0', 'max:100'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
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
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($reward->image && Storage::disk('public')->exists($reward->image)) {
                Storage::disk('public')->delete($reward->image);
            }
            $data['image'] = $request->file('image')->store('rewards', 'public');
        }

        $reward->update($data);

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Reward updated successfully.');
    }

    /**
     * Remove the specified reward.
     */
    public function destroy(Reward $reward)
    {
        $reward->delete();

        return redirect()->route('admin.rewards.index')
            ->with('success', 'Reward deleted successfully.');
    }

    /**
     * Toggle reward status.
     */
    public function toggleStatus(Reward $reward)
    {
        $reward->is_active = !$reward->is_active;
        $reward->save();

        return back()->with('success', 'Reward status updated.');
    }
}
