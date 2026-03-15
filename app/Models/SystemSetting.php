<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        return Cache::remember("system_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, $value, string $type = 'string'): void
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $type === 'json' ? json_encode($value) : $value, 'type' => $type]
        );

        Cache::forget("system_setting_{$key}");
        Cache::forget('system_settings_all');
    }

    /**
     * Get all settings as key-value pairs.
     */
    public static function getAll(): array
    {
        return Cache::remember('system_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Check if a boolean setting is enabled.
     */
    public static function isEnabled(string $key): bool
    {
        return (bool) static::getValue($key, false);
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('system_settings_all');
        
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("system_setting_{$key}");
        }
    }
}
