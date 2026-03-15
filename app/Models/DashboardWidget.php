<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class DashboardWidget extends Model
{
    protected $fillable = ['widget_name', 'widget_key', 'role', 'status', 'sort_order', 'config'];

    protected $casts = [
        'sort_order' => 'integer',
        'config' => 'array',
    ];

    /**
     * Get active widgets for a role.
     */
    public static function getForRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("dashboard_widgets_{$role}", 3600, function () use ($role) {
            return static::where('role', $role)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Get all widgets grouped by role.
     */
    public static function getAllGroupedByRole(): array
    {
        return Cache::remember('dashboard_widgets_grouped', 3600, function () {
            return static::orderBy('role')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('role')
                ->toArray();
        });
    }

    /**
     * Clear widget cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('dashboard_widgets_grouped');
        Cache::forget('dashboard_widgets_all');
        
        $roles = ['Admin', 'Doctor', 'Store', 'MR', 'End User'];
        foreach ($roles as $role) {
            Cache::forget("dashboard_widgets_{$role}");
        }
    }

    /**
     * Scope for active widgets.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for specific role.
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
