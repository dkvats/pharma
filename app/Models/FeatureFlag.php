<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    protected $fillable = ['flag_key', 'name', 'description', 'enabled', 'rollout_percentage', 'target_roles'];

    protected $casts = [
        'enabled' => 'boolean',
        'rollout_percentage' => 'integer',
        'target_roles' => 'array',
    ];

    /**
     * Check if a feature flag is enabled.
     */
    public static function isEnabled(string $key): bool
    {
        return Cache::remember("feature_flag_{$key}", 3600, function () use ($key) {
            $flag = static::where('flag_key', $key)->first();
            return $flag ? $flag->enabled : false;
        });
    }

    /**
     * Check if a feature is enabled for a specific role.
     */
    public static function isEnabledForRole(string $key, string $role): bool
    {
        $flag = static::where('flag_key', $key)->first();
        
        if (!$flag || !$flag->enabled) {
            return false;
        }

        // If no target roles specified, enabled for all
        if (empty($flag->target_roles)) {
            return true;
        }

        return in_array($role, $flag->target_roles);
    }

    /**
     * Check with rollout percentage.
     */
    public static function isEnabledForUser(string $key, int $userId): bool
    {
        $flag = static::where('flag_key', $key)->first();
        
        if (!$flag || !$flag->enabled) {
            return false;
        }

        // If no rollout percentage, it's 100%
        if (!$flag->rollout_percentage) {
            return true;
        }

        // Use user ID to deterministically decide
        return ($userId % 100) < $flag->rollout_percentage;
    }

    /**
     * Enable a feature flag.
     */
    public function enable(): void
    {
        $this->update(['enabled' => true]);
        $this->clearCache();
    }

    /**
     * Disable a feature flag.
     */
    public function disable(): void
    {
        $this->update(['enabled' => false]);
        $this->clearCache();
    }

    /**
     * Clear feature flag cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('feature_flags_all');
        
        $keys = static::pluck('flag_key');
        foreach ($keys as $key) {
            Cache::forget("feature_flag_{$key}");
        }
    }

    /**
     * Scope for enabled flags.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
