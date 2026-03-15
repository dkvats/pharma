<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notification Service
 *
 * Handles sending notifications using CMS templates.
 * Supports email notifications with fallback to defaults.
 *
 * Valid template keys (must match notification_templates table):
 *   order_placed        — new order created
 *   order_approved      — order approved by admin
 *   order_delivered     — order delivered
 *   spin_reward_won     — doctor won a spin reward
 *   target_achieved     — doctor achieved monthly target
 *   doctor_registered   — doctor account approved/registered
 *   store_registered    — store account approved/registered
 *   welcome_user        — new user welcome message
 *
 * Usage:
 *   NotificationService::sendOrderConfirmation($order, $user);
 *   NotificationService::sendSpinReward($reward, $user);
 *   NotificationService::sendDoctorApproval($doctor, $status);
 */
class NotificationService
{
    /**
     * Send order confirmation notification (new order placed).
     * Uses template key: order_placed
     *
     * @param \App\Models\Order $order
     * @param \App\Models\User $user
     * @return bool
     */
    public static function sendOrderConfirmation($order, User $user): bool
    {
        $template = NotificationTemplate::getByKey('order_placed');

        $variables = [
            'user_name'    => $user->name,
            'order_number' => $order->order_number,
            'order_total'  => number_format($order->total_amount, 2),
            'order_status' => ucfirst($order->status),
            'order_date'   => $order->created_at->format('Y-m-d H:i'),
        ];

        return self::send($template, $user, $variables, 'Order Confirmation', "Your order #{$order->order_number} has been received.");
    }

    /**
     * Send order status update notification.
     * Maps status to the correct DB template key:
     *   approved  → order_approved
     *   delivered → order_delivered
     *   other     → fallback default (no DB template)
     *
     * @param \App\Models\Order $order
     * @param \App\Models\User $user
     * @param string $status
     * @return bool
     */
    public static function sendOrderStatusUpdate($order, User $user, string $status): bool
    {
        // Map order status to the correct database template key
        $templateKey = match ($status) {
            'approved'  => 'order_approved',
            'delivered' => 'order_delivered',
            default     => null,
        };

        $template = $templateKey ? NotificationTemplate::getByKey($templateKey) : null;

        $variables = [
            'user_name'      => $user->name,
            'order_number'   => $order->order_number,
            'order_status'   => ucfirst($status),
            'status_message' => self::getStatusMessage($status),
        ];

        return self::send($template, $user, $variables, 'Order Status Update', "Your order #{$order->order_number} status: {$status}");
    }

    /**
     * Send spin reward notification.
     * Uses template key: spin_reward_won
     *
     * @param \App\Models\Reward $reward
     * @param \App\Models\User $user
     * @return bool
     */
    public static function sendSpinReward($reward, User $user): bool
    {
        $template = NotificationTemplate::getByKey('spin_reward_won');

        $variables = [
            'user_name'    => $user->name,
            'reward_name'  => $reward->name,
            'reward_value' => number_format($reward->value, 2),
            'reward_type'  => ucfirst($reward->type),
        ];

        return self::send($template, $user, $variables, 'Spin Reward', "Congratulations! You won: {$reward->name}");
    }

    /**
     * Send doctor approval notification.
     * Approved  → uses template key: doctor_registered
     * Rejected  → no DB template; uses default fallback body
     *
     * @param \App\Models\User $doctor
     * @param string $status 'approved', 'rejected', 'pending'
     * @param string|null $rejectionReason
     * @return bool
     */
    public static function sendDoctorApproval(User $doctor, string $status, ?string $rejectionReason = null): bool
    {
        // Only 'approved' has a matching DB template key
        $template = $status === 'approved'
            ? NotificationTemplate::getByKey('doctor_registered')
            : null;

        $variables = [
            'user_name'        => $doctor->name,
            'doctor_name'      => $doctor->name,
            'login_email'      => $doctor->email,
            'status'           => ucfirst($status),
            'rejection_reason' => $rejectionReason ?? 'N/A',
            'login_url'        => route('login'),
        ];

        $defaultSubject = $status === 'approved'
            ? 'Your Doctor Account Has Been Approved'
            : 'Doctor Account Status Update';
        $defaultBody = $status === 'approved'
            ? "Congratulations! Your doctor account has been approved. You can now login and start using the platform."
            : "Your doctor account status has been updated to: {$status}." . ($rejectionReason ? " Reason: {$rejectionReason}" : '');

        return self::send($template, $doctor, $variables, $defaultSubject, $defaultBody);
    }

    /**
     * Send store approval notification.
     * Approved  → uses template key: store_registered
     * Rejected  → no DB template; uses default fallback body
     *
     * @param \App\Models\User $store
     * @param string $status 'approved', 'rejected', 'pending'
     * @param string|null $rejectionReason
     * @return bool
     */
    public static function sendStoreApproval(User $store, string $status, ?string $rejectionReason = null): bool
    {
        // Only 'approved' has a matching DB template key
        $template = $status === 'approved'
            ? NotificationTemplate::getByKey('store_registered')
            : null;

        $variables = [
            'user_name'        => $store->name,
            'store_name'       => $store->name,
            'store_email'      => $store->email,
            'login_email'      => $store->email,
            'status'           => ucfirst($status),
            'rejection_reason' => $rejectionReason ?? 'N/A',
            'login_url'        => route('login'),
        ];

        $defaultSubject = $status === 'approved'
            ? 'Your Store Account Has Been Approved'
            : 'Store Account Status Update';
        $defaultBody = $status === 'approved'
            ? "Congratulations! Your store account has been approved. You can now login and start using the platform."
            : "Your store account status has been updated to: {$status}." . ($rejectionReason ? " Reason: {$rejectionReason}" : '');

        return self::send($template, $store, $variables, $defaultSubject, $defaultBody);
    }

    /**
     * Send a notification using a template.
     *
     * @param NotificationTemplate|null $template
     * @param User $user
     * @param array $variables
     * @param string $defaultSubject
     * @param string $defaultBody
     * @return bool
     */
    protected static function send(?NotificationTemplate $template, User $user, array $variables, string $defaultSubject, string $defaultBody): bool
    {
        // Check if email notifications are enabled
        if (!SystemSettingService::isEmailNotificationEnabled()) {
            Log::info('Email notifications disabled. Skipping notification.', [
                'user_id' => $user->id,
                'template' => $template?->template_key ?? 'default',
            ]);
            return true;
        }

        // Get subject and body from template or use defaults
        if ($template) {
            $rendered = $template->render($variables);
            $subject = $rendered['subject'];
            $body = $rendered['body'];
        } else {
            $subject = $defaultSubject;
            $recipientName = $variables['user_name'] ?? $variables['doctor_name'] ?? $variables['store_name'] ?? 'User';
            $body = "Dear {$recipientName},\n\n{$defaultBody}\n\nBest regards,\nThe Pharma Team";
        }

        try {
            // For now, we'll log the notification
            // In production, integrate with actual email service
            Log::info('Notification sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'subject' => $subject,
            ]);

            // TODO: Integrate with actual mail sending
            // Mail::to($user->email)->send(new NotificationMail($subject, $body));
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get a human-readable status message.
     *
     * @param string $status
     * @return string
     */
    protected static function getStatusMessage(string $status): string
    {
        return match ($status) {
            'pending' => 'Your order is being processed.',
            'approved' => 'Your order has been approved and will be processed soon.',
            'processing' => 'Your order is being prepared.',
            'shipped' => 'Your order has been shipped.',
            'delivered' => 'Your order has been delivered.',
            'cancelled' => 'Your order has been cancelled.',
            default => 'Your order status has been updated.',
        };
    }

    /**
     * Send a custom notification using a template key.
     *
     * @param string $templateKey
     * @param User $user
     * @param array $variables
     * @return bool
     */
    public static function sendFromTemplate(string $templateKey, User $user, array $variables = []): bool
    {
        $template = NotificationTemplate::getByKey($templateKey);
        
        if (!$template) {
            Log::warning("Notification template not found: {$templateKey}");
            return false;
        }

        $defaultVariables = [
            'user_name' => $user->name,
            'user_email' => $user->email,
        ];

        $variables = array_merge($defaultVariables, $variables);

        return self::send($template, $user, $variables, 'Notification', 'You have a new notification.');
    }
}
