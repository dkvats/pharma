<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MR\Doctor;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorApprovalController extends Controller
{
    /**
     * Display pending MR doctors for approval
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $query = Doctor::with(['creator', 'user', 'area', 'city', 'state'])
            ->whereNotNull('user_id'); // Only show doctors with user accounts
        
        if ($status === 'all') {
            // Show all MR-created doctors
        } else {
            $query->where('status', $status);
        }
        
        $doctors = $query->latest()->paginate(15);
        
        // Stats
        $stats = [
            'pending' => Doctor::pending()->count(),
            'approved' => Doctor::approved()->count(),
            'rejected' => Doctor::rejected()->count(),
            'inactive' => Doctor::where('status', 'inactive')->count(),
        ];
        
        return view('admin.doctors.approval.index', compact('doctors', 'stats', 'status'));
    }
    
    /**
     * Show doctor details for approval review
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['creator', 'user', 'area', 'city', 'district', 'state', 'approver']);
        
        return view('admin.doctors.approval.show', compact('doctor'));
    }
    
    /**
     * Approve a doctor
     */
    public function approve(Request $request, Doctor $doctor)
    {
        if (!$doctor->isPending()) {
            return back()->with('error', 'Only pending doctors can be approved.');
        }
        
        // Update doctor status
        $doctor->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
        ]);
        
        // Activate linked user account
        if ($doctor->user) {
            $doctor->user->update([
                'status' => 'active',
            ]);
        }
        
        // Log activity
        logActivity(
            'Doctor Approved',
            'MR Doctor',
            $doctor->id,
            "Doctor '{$doctor->name}' ({$doctor->doctor_code}) approved by " . auth()->user()->name
        );
        
        return redirect()->route('admin.doctors.approval.index')
            ->with('success', "Doctor '{$doctor->name}' has been approved and can now receive referrals.");
    }
    
    /**
     * Reject a doctor
     */
    public function reject(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:500'],
        ]);
        
        if (!$doctor->isPending()) {
            return back()->with('error', 'Only pending doctors can be rejected.');
        }
        
        // Update doctor status
        $doctor->update([
            'status' => 'rejected',
            'is_active' => false,
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        
        // Keep user account inactive
        if ($doctor->user) {
            $doctor->user->update([
                'status' => 'inactive',
            ]);
        }
        
        // Log activity
        logActivity(
            'Doctor Rejected',
            'MR Doctor',
            $doctor->id,
            "Doctor '{$doctor->name}' ({$doctor->doctor_code}) rejected by " . auth()->user()->name
        );
        
        return redirect()->route('admin.doctors.approval.index')
            ->with('success', "Doctor '{$doctor->name}' has been rejected.");
    }
    
    /**
     * Deactivate an approved doctor
     */
    public function deactivate(Request $request, Doctor $doctor)
    {
        if (!$doctor->isApproved()) {
            return back()->with('error', 'Only approved doctors can be deactivated.');
        }
        
        $doctor->update([
            'status' => 'inactive',
            'is_active' => false,
        ]);
        
        // Deactivate user account
        if ($doctor->user) {
            $doctor->user->update([
                'status' => 'inactive',
            ]);
        }
        
        logActivity(
            'Doctor Deactivated',
            'MR Doctor',
            $doctor->id,
            "Doctor '{$doctor->name}' ({$doctor->doctor_code}) deactivated by " . auth()->user()->name
        );
        
        return redirect()->route('admin.doctors.approval.index')
            ->with('success', "Doctor '{$doctor->name}' has been deactivated.");
    }
    
    /**
     * Reactivate an inactive doctor
     */
    public function reactivate(Request $request, Doctor $doctor)
    {
        if (!$doctor->isInactive()) {
            return back()->with('error', 'Only inactive doctors can be reactivated.');
        }
        
        $doctor->update([
            'status' => 'approved',
            'is_active' => true,
        ]);
        
        // Reactivate user account
        if ($doctor->user) {
            $doctor->user->update([
                'status' => 'active',
            ]);
        }
        
        logActivity(
            'Doctor Reactivated',
            'MR Doctor',
            $doctor->id,
            "Doctor '{$doctor->name}' ({$doctor->doctor_code}) reactivated by " . auth()->user()->name
        );
        
        return redirect()->route('admin.doctors.approval.index')
            ->with('success', "Doctor '{$doctor->name}' has been reactivated.");
    }
}
