<?php

namespace App\Notifications;

use App\Models\MR\StoreUpdateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreUpdateRejected extends Notification implements ShouldQueue
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
            ->subject('📝 Store Update Request Rejected - ' . $this->store->store_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your store update request has been reviewed and rejected.')
            ->line('Store: **' . $this->store->store_name . '** (' . $this->store->store_code . ')')
            ->line('**Reason for Rejection:**')
            ->line($this->updateRequest->rejection_reason)
            ->line('You can submit a revised update request with the corrections.')
            ->action('Submit New Update', url(route('mr.stores.edit', $this->store)))
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
            'type' => 'store_update_rejected',
            'store_id' => $this->store->id,
            'store_name' => $this->store->store_name,
            'store_code' => $this->store->store_code,
            'update_request_id' => $this->updateRequest->id,
            'rejection_reason' => $this->updateRequest->rejection_reason,
            'rejected_by' => $this->updateRequest->approver?->name,
            'rejected_at' => $this->updateRequest->approved_at,
        ];
    }
}
