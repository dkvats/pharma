<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\SpinOverride;
use App\Models\User;
use Illuminate\Http\Request;

class SpinControlController extends Controller
{
    /**
     * Display the spin control dashboard
     */
    public function index()
    {
        $doctors = User::role('Doctor')->where('status', 'active')->get();
        $rewards = Reward::where('is_active', true)->get();
        
        // Get pending overrides with doctor and reward info
        $pendingOverrides = SpinOverride::with(['doctor', 'reward'])
            ->where('is_used', false)
            ->latest()
            ->paginate(20);
        
        // Get override history
        $overrideHistory = SpinOverride::with(['doctor', 'reward'])
            ->where('is_used', true)
            ->latest()
            ->paginate(20);
        
        return view('admin.spin-control.index', compact('doctors', 'rewards', 'pendingOverrides', 'overrideHistory'));
    }

    /**
     * Store a new spin override
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:users,id'],
            'reward_id' => ['required', 'exists:rewards,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        // Verify the user is a doctor
        $doctor = User::find($validated['doctor_id']);
        if (!$doctor->hasRole('Doctor')) {
            return back()->with('error', 'Selected user is not a doctor.');
        }

        // Check if doctor already has a pending override (safety check)
        if (SpinOverride::hasPendingForDoctor($validated['doctor_id'])) {
            return back()->with('error', 'Doctor already has a pending reward assignment. Delete it first or wait for it to be used.');
        }

        SpinOverride::create([
            'doctor_id' => $validated['doctor_id'],
            'reward_id' => $validated['reward_id'],
            'is_used' => false,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('success', 'Reward assigned to doctor successfully.');
    }

    /**
     * Delete a pending spin override
     */
    public function destroy(SpinOverride $override)
    {
        if ($override->is_used) {
            return back()->with('error', 'Cannot delete an override that has already been used.');
        }

        $override->delete();

        return back()->with('success', 'Pending reward assignment deleted.');
    }
}
