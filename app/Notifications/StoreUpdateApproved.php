<?php

namespace App\Notifications;

use App\Models\MR\StoreUpdateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreUpdateApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $updateRequest;
    public $store;

    /**
     * Create a new notification instance.
     */
    public function __construct(StoreUpdateRequest $updateRequest)
    {
        $this->updateRequest = $updateRequest;
        $this->store = $updateRequest->store;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Store Update Approved - ' . $this->store->store_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your store update request has been approved.')
            ->line('Store: **' . $this->store->store_name . '** (' . $this->store->store_code . ')')
            ->line('The following changes have been applied:')
            ->action('View Your Store', url(route('mr.stores.show', $this->store)))
            ->line('If you have any questions, please contact the admin team.')
            ->salutation('Best Regards, Pharma Admin Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'store_update_approved',
            'store_id' => $this->store->id,
            'store_name' => $this->store->store_name,
            'store_code' => $this->store->store_code,
            'update_request_id' => $this->updateRequest->id,
            'approved_by' => $this->updateRequest->approver?->name,
            'approved_at' => $this->updateRequest->approved_at,
        ];
    }
}
