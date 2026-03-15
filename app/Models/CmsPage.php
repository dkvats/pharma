<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsPage extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'status', 'meta_title', 'meta_description'];

    /**
     * Get a page by slug.
     */
    public static function getBySlug(string $slug): ?self
    {
        return Cache::remember("cms_page_{$slug}", 3600, function () use ($slug) {
            return static::where('slug', $slug)->where('status', 'active')->first();
        });
    }

    /**
     * Get all active pages.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('cms_pages_active', 3600, function () {
            return static::where('status', 'active')->orderBy('title')->get();
        });
    }

    /**
     * Clear page cache.
     */
    public static function clearCache(?string $slug = null): void
    {
        Cache::forget('cms_pages_active');
        Cache::forget('cms_pages_all');
        
        if ($slug) {
            Cache::forget("cms_page_{$slug}");
        } else {
            $slugs = static::pluck('slug');
            foreach ($slugs as $s) {
                Cache::forget("cms_page_{$s}");
            }
        }
    }

    /**
     * Scope for active pages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if page is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the URL for this page.
     */
    public function getUrlAttribute(): string
    {
        return url("/page/{$this->slug}");
    }
}
