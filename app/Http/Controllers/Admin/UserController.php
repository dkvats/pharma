<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Exclude Super Admin users - Admin cannot manage Super Admin
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Super Admin');
        });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->with('roles')->latest()->paginate(10);
        
        // Exclude Super Admin role from the roles list
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Exclude Super Admin role - Admin cannot create Super Admin
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'exists:roles,name'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            // MR-specific fields (optional)
            'employee_code' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'assigned_area' => ['nullable', 'string', 'max:255'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ];

        // Add MR-specific fields only if role is MR
        if ($validated['role'] === 'MR') {
            $userData['employee_code'] = $validated['employee_code'];
            $userData['designation'] = $validated['designation'] ?? 'Medical Representative';
            $userData['assigned_area'] = $validated['assigned_area'];
        }

        $user = User::create($userData);

        $user->assignRole($validated['role']);

        // Log user creation
        logActivity(
            'User Created',
            'User',
            $user->id,
            "{$validated['role']} '{$user->name}' created with code: {$user->unique_code}"
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully. Referral Code: ' . $user->unique_code);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Prevent editing Super Admin - role hierarchy protection
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action. Cannot edit Super Admin.');
        }

        // Exclude Super Admin role from roles list
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating Super Admin - role hierarchy protection
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action. Cannot update Super Admin.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:6'],
            'role' => ['required', 'exists:roles,name'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            // MR-specific fields (optional)
            'employee_code' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'assigned_area' => ['nullable', 'string', 'max:255'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'status' => $validated['status'],
        ];

        // Handle MR-specific fields
        if ($validated['role'] === 'MR') {
            $userData['employee_code'] = $validated['employee_code'];
            $userData['designation'] = $validated['designation'] ?? 'Medical Representative';
            $userData['assigned_area'] = $validated['assigned_area'];
        } else {
            // Clear MR fields if role is not MR
            $userData['employee_code'] = null;
            $userData['designation'] = null;
            $userData['assigned_area'] = null;
        }

        $user->update($userData);

        // Update password if provided
        if ($validated['password']) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update role
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Soft delete the specified user (move to trash).
     */
    public function destroy(User $user)
    {
        // Prevent deleting Super Admin - role hierarchy protection
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action. Cannot delete Super Admin.');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete(); // soft delete

        return back()->with('success', 'User moved to Trash.');
    }

    /**
     * Display a listing of trashed users.
     */
    public function trash()
    {
        // Exclude Super Admin from trash view as well
        $users = User::onlyTrashed()
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Super Admin');
            })
            ->with('roles')
            ->paginate(10);
        return view('admin.users.trash', compact('users'));
    }

    /**
     * Restore the specified user from trash.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // Prevent restoring Super Admin - role hierarchy protection
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action. Cannot restore Super Admin.');
        }

        $user->restore();

        return back()->with('success', 'User restored successfully.');
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling Super Admin status - role hierarchy protection
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action. Cannot modify Super Admin status.');
        }

        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own status.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', 'User status updated to ' . $user->status);
    }

}
