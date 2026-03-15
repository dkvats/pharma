<?php

namespace App\Http\Middleware;

use App\Services\SystemSettingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleEnabled
{
    /**
     * Check if a module is enabled before allowing access.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Super Admin can always access
        if (auth()->user() && auth()->user()->hasRole('Super Admin')) {
            return $next($request);
        }

        // Check if module is enabled
        if (!SystemSettingService::isModuleActive($module)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "The {$module} module is currently disabled by Super Admin."
                ], 403);
            }

            // Redirect to dashboard with message
            $dashboardRoute = $this->getDashboardRoute();
            
            return redirect()->route($dashboardRoute)
                ->with('error', "The {$module} module is currently disabled by Super Admin.");
        }

        return $next($request);
    }

    /**
     * Get the appropriate dashboard route based on user role.
     */
    private function getDashboardRoute(): string
    {
        $user = auth()->user();
        
        if (!$user) {
            return 'login';
        }

        return match ($user->roles->first()?->name) {
            'Admin', 'Sub Admin' => 'admin.dashboard',
            'Doctor' => 'doctor.dashboard',
            'Store' => 'store.dashboard',
            'MR' => 'mr.dashboard',
            'Super Admin' => 'super-admin.dashboard',
            default => 'dashboard',
        };
    }
}
