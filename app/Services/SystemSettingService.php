<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\Module;
use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

/**
 * System Setting Service
 * 
 * Provides a unified interface for accessing system settings throughout the application.
 * All settings are cached for performance.
 * 
 * Usage:
 *   SystemSettingService::get('spin_enabled')
 *   SystemSettingService::isModuleActive('spin')
 *   SystemSettingService::isFeatureEnabled('new_spin_algorithm')
 */
class SystemSettingService
{
    /**
     * Get a system setting value.
     *
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return SystemSetting::getValue($key, $default);
    }

    /**
     * Set a system setting value.
     *
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @param string $type Value type (string, boolean, integer, json)
     * @return void
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        SystemSetting::setValue($key, $value, $type);
    }

    /**
     * Check if a boolean setting is enabled.
     *
     * @param string $key Setting key
     * @return bool
     */
    public static function isEnabled(string $key): bool
    {
        return SystemSetting::isEnabled($key);
    }

    /**
     * Check if a module is active.
     *
     * @param string $slug Module slug
     * @return bool
     */
    public static function isModuleActive(string $slug): bool
    {
        return Module::isActive($slug);
    }

    /**
     * Check if a feature flag is enabled.
     *
     * @param string $key Feature flag key
     * @return bool
     */
    public static function isFeatureEnabled(string $key): bool
    {
        return FeatureFlag::isEnabled($key);
    }

    /**
     * Check if a feature is enabled for a specific role.
     *
     * @param string $key Feature flag key
     * @param string $role Role name
     * @return bool
     */
    public static function isFeatureEnabledForRole(string $key, string $role): bool
    {
        return FeatureFlag::isEnabledForRole($key, $role);
    }

    /**
     * Check if a feature is enabled for a specific user.
     *
     * @param string $key Feature flag key
     * @param int $userId User ID
     * @return bool
     */
    public static function isFeatureEnabledForUser(string $key, int $userId): bool
    {
        return FeatureFlag::isEnabledForUser($key, $userId);
    }

    /**
     * Get the doctor target quantity.
     *
     * @return int
     */
    public static function getDoctorTargetQuantity(): int
    {
        return (int) self::get('doctor_target_quantity', 30);
    }

    /**
     * Get leaderboard refresh interval.
     *
     * @return int Seconds
     */
    public static function getLeaderboardRefreshInterval(): int
    {
        return (int) self::get('leaderboard_refresh_interval', 300);
    }

    /**
     * Check if spin system is enabled.
     *
     * @return bool
     */
    public static function isSpinEnabled(): bool
    {
        return self::isEnabled('spin_enabled') && self::isModuleActive('spin');
    }

    /**
     * Check if offers system is enabled.
     *
     * @return bool
     */
    public static function isOffersEnabled(): bool
    {
        return self::isEnabled('offers_enabled') && self::isModuleActive('offers');
    }

    /**
     * Check if MR module is enabled.
     *
     * @return bool
     */
    public static function isMrModuleEnabled(): bool
    {
        return self::isEnabled('mr_module_enabled') && self::isModuleActive('mr');
    }

    /**
     * Check if homepage CMS is enabled.
     *
     * @return bool
     */
    public static function isHomepageCmsEnabled(): bool
    {
        return self::isEnabled('homepage_cms_enabled') && self::isModuleActive('homepage-cms');
    }

    /**
     * Check if grand draw is enabled.
     *
     * @return bool
     */
    public static function isGrandDrawEnabled(): bool
    {
        return self::isEnabled('grand_draw_enabled') && self::isModuleActive('grand-draw');
    }

    /**
     * Check if order auto-approve is enabled.
     *
     * @return bool
     */
    public static function isOrderAutoApproveEnabled(): bool
    {
        return self::isEnabled('order_auto_approve');
    }

    /**
     * Check if email notifications are enabled.
     *
     * @return bool
     */
    public static function isEmailNotificationEnabled(): bool
    {
        return self::isEnabled('notification_email_enabled');
    }

    /**
     * Check if site is in maintenance mode.
     *
     * @return bool
     */
    public static function isMaintenanceMode(): bool
    {
        return self::isEnabled('maintenance_mode');
    }

    /**
     * Get all system settings.
     *
     * @return array
     */
    public static function getAll(): array
    {
        return SystemSetting::getAll();
    }

    /**
     * Get all active modules.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveModules()
    {
        return Module::getActive();
    }

    /**
     * Clear all caches.
     *
     * @return void
     */
    public static function clearAllCaches(): void
    {
        SystemSetting::clearCache();
        Module::clearCache();
        FeatureFlag::clearCache();
    }
}
