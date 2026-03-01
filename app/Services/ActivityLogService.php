<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin(): ActivityLog
    {
        return self::log('login', null, 'User logged in');
    }

    /**
     * Log logout activity
     */
    public static function logLogout(): ActivityLog
    {
        return self::log('logout', null, 'User logged out');
    }

    /**
     * Log model creation
     */
    public static function logCreated(Model $model, ?string $description = null): ActivityLog
    {
        return self::log(
            'created',
            $model,
            $description ?? class_basename($model) . ' created',
            null,
            $model->toArray()
        );
    }

    /**
     * Log model update
     */
    public static function logUpdated(Model $model, array $oldValues, ?string $description = null): ActivityLog
    {
        return self::log(
            'updated',
            $model,
            $description ?? class_basename($model) . ' updated',
            $oldValues,
            $model->toArray()
        );
    }

    /**
     * Log model deletion
     */
    public static function logDeleted(Model $model, ?string $description = null): ActivityLog
    {
        return self::log(
            'deleted',
            $model,
            $description ?? class_basename($model) . ' deleted',
            $model->toArray(),
            null
        );
    }

    /**
     * Log order placement
     */
    public static function logOrderPlaced($order): ActivityLog
    {
        return self::log(
            'order_placed',
            $order,
            'Order #' . $order->order_number . ' placed',
            null,
            $order->toArray()
        );
    }

    /**
     * Log order status change
     */
    public static function logOrderStatusChanged($order, string $oldStatus, string $newStatus): ActivityLog
    {
        return self::log(
            'order_status_changed',
            $order,
            'Order #' . $order->order_number . ' status changed from ' . $oldStatus . ' to ' . $newStatus,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }

    /**
     * Log spin activity
     */
    public static function logSpin($spinHistory, $reward): ActivityLog
    {
        return self::log(
            'spin_completed',
            $spinHistory,
            'Doctor spun and won: ' . $reward->name,
            null,
            [
                'reward_id' => $reward->id,
                'reward_name' => $reward->name,
                'reward_value' => $reward->value,
            ]
        );
    }

    /**
     * Get recent activities
     */
    public static function getRecent(int $limit = 50)
    {
        return ActivityLog::with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific user
     */
    public static function getForUser(int $userId, int $limit = 50)
    {
        return ActivityLog::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities for a specific model
     */
    public static function getForModel(Model $model, int $limit = 50)
    {
        return ActivityLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Log prescription download
     */
    public static function logPrescriptionDownload($order): ActivityLog
    {
        return self::log('prescription_download', $order, 'Prescription downloaded for order ' . $order->order_number);
    }

    /**
     * Log prescription view
     */
    public static function logPrescriptionView($order): ActivityLog
    {
        return self::log('prescription_view', $order, 'Prescription viewed for order ' . $order->order_number);
    }

    /**
     * Log failed prescription access attempt
     */
    public static function logPrescriptionAccessDenied($order, string $action, string $reason): ActivityLog
    {
        return self::log(
            'prescription_access_denied',
            $order,
            "Prescription {$action} access DENIED for order {$order->order_number}: {$reason}",
            null,
            [
                'attempted_action' => $action,
                'denial_reason' => $reason,
                'user_id' => auth()->id(),
                'user_role' => auth()->user()?->roles->pluck('name')->first(),
                'ip_address' => request()->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]
        );
    }
}
