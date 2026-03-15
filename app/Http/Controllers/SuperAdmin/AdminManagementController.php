<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Sub Admin', 'Super Admin']);
        })->with('roles')->paginate(20);
        
        $roles = Role::whereIn('name', ['Admin', 'Sub Admin', 'Super Admin'])->get();

        // Stats for the view
        $stats = [
            'total' => User::role(['Admin', 'Sub Admin', 'Super Admin'])->count(),
            'admins' => User::role('Admin')->count(),
            'sub_admins' => User::role('Sub Admin')->count(),
            'super_admins' => User::role('Super Admin')->count(),
            'active' => User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Admin', 'Sub Admin', 'Super Admin']);
            })->where('status', 'active')->count(),
        ];
        
        return view('super-admin.admin-management.index', compact('admins', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'Sub Admin', 'Super Admin'])->get();
        return view('super-admin.admin-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Admin,Sub Admin,Super Admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'unique_code' => strtoupper(Str::random(8)),
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        logActivity('Admin Created', $user, "Super Admin created {$validated['role']}: {$user->email}");

        return redirect()->route('super-admin.admins.index')
            ->with('success', "Admin '{$user->name}' created successfully.");
    }

    public function edit(User $admin)
    {
        $roles = Role::whereIn('name', ['Admin', 'Sub Admin', 'Super Admin'])->get();
        $admin->load('roles');
        
        return view('super-admin.admin-management.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $admin->id,
            'role' => 'required|in:Admin,Sub Admin,Super Admin',
            'status' => 'required|in:active,inactive',
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        // Sync role
        $admin->syncRoles([$validated['role']]);

        logActivity('Admin Updated', $admin, "Super Admin updated admin: {$admin->email}");

        return redirect()->route('super-admin.admins.index')
            ->with('success', "Admin '{$admin->name}' updated successfully.");
    }

    public function destroy(User $admin)
    {
        // Prevent self-deletion
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $admin->name;
        $admin->delete();

        logActivity('Admin Deleted', null, "Super Admin deleted admin: {$name}");

        return back()->with('success', "Admin '{$name}' deleted successfully.");
    }

    public function toggleStatus(User $admin)
    {
        // Prevent self-deactivation
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own status.');
        }

        $admin->status = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->save();

        logActivity('Admin Status Changed', $admin, "Admin '{$admin->name}' set to {$admin->status}");

        return back()->with('success', "Admin '{$admin->name}' is now {$admin->status}.");
    }

    public function resetPassword(Request $request, User $admin)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        logActivity('Admin Password Reset', $admin, "Password reset for: {$admin->email}");

        return back()->with('success', "Password reset successfully for '{$admin->name}'.");
    }
}
