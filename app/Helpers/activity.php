<?php

use App\Models\ActivityLog;

if (!function_exists('logActivity')) {
    /**
     * Log system activity
     *
     * @param string $action Action performed
     * @param string|null $entityType Type of entity (Order, User, etc.)
     * @param int|null $entityId ID of the entity
     * @param string|null $description Additional description
     * @return ActivityLog|null
     */
    function logActivity($action, $entityType = null, $entityId = null, $description = null)
    {
        try {
            $user = auth()->user();
            
            return ActivityLog::create([
                'user_id' => $user?->id,
                'role' => $user?->roles?->first()?->name,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Exception $e) {
            // Fail silently - never break the system
            \Log::error('Activity log failed: ' . $e->getMessage());
            return null;
        }
    }
}
