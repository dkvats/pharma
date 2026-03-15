<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class UiSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Get a UI setting value.
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("ui_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a UI setting value.
     */
    public static function setValue(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("ui_setting_{$key}");
        Cache::forget('ui_settings_all');
    }

    /**
     * Get all UI settings.
     */
    public static function getAll(): array
    {
        return Cache::remember('ui_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear UI settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('ui_settings_all');
        
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("ui_setting_{$key}");
        }
    }

    /**
     * Get primary color.
     */
    public static function getPrimaryColor(): string
    {
        return static::getValue('site_primary_color', '#2563eb');
    }

    /**
     * Get secondary color.
     */
    public static function getSecondaryColor(): string
    {
        return static::getValue('site_secondary_color', '#1e3a5f');
    }
}
