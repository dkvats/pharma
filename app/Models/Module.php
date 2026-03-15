<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Module extends Model
{
    protected $fillable = ['module_name', 'slug', 'status', 'description', 'icon', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Check if a module is active.
     */
    public static function isActive(string $slug): bool
    {
        return Cache::remember("module_active_{$slug}", 3600, function () use ($slug) {
            return static::where('slug', $slug)->where('status', 'active')->exists();
        });
    }

    /**
     * Get all active modules.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('modules_active', 3600, function () {
            return static::where('status', 'active')->orderBy('sort_order')->get();
        });
    }

    /**
     * Enable a module.
     */
    public function enable(): void
    {
        $this->update(['status' => 'active']);
        $this->clearCache();
    }

    /**
     * Disable a module.
     */
    public function disable(): void
    {
        $this->update(['status' => 'inactive']);
        $this->clearCache();
    }

    /**
     * Clear module cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('modules_active');
        Cache::forget('modules_all');
        
        $slugs = static::pluck('slug');
        foreach ($slugs as $slug) {
            Cache::forget("module_active_{$slug}");
        }
    }

    /**
     * Scope for active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if this module instance is active.
     */
    public function hasActiveStatus(): bool
    {
        return $this->status === 'active';
    }
}
