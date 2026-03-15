<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Check if user is active
            if (!Auth::user()->isActive()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            // Update login tracking
            Auth::user()->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Log login activity
            ActivityLogService::logLogin();

            // Use direct redirect instead of intended() to ensure correct dashboard
            return redirect($this->getDashboardRoute());
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users'],
            'address' => ['nullable', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        // Assign End User role by default
        $user->assignRole('End User');

        // Create cart and wishlist for the new user
        $user->cart()->create([]);
        $user->wishlist()->create([]);

        Auth::login($user);

        return redirect($this->getDashboardRoute());
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Log logout activity before actual logout
        ActivityLogService::logLogout();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the appropriate dashboard route based on user role.
     */
    private function getDashboardRoute(): string
    {
        $user = Auth::user();

        // Super Admin must be checked FIRST - has platform control panel
        if ($user->hasRole('Super Admin')) {
            return route('super-admin.dashboard');
        } elseif ($user->hasRole('Admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('Doctor')) {
            return route('doctor.dashboard');
        } elseif ($user->hasRole('Store')) {
            return route('store.dashboard');
        } elseif ($user->hasRole('MR')) {
            return route('mr.dashboard');
        } else {
            return route('dashboard');
        }
    }
}
