<?php

namespace App\Notifications;

use App\Models\MR\StoreUpdateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreUpdateRequested extends Notification implements ShouldQueue
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
            ->subject('📝 New Store Update Request - Review Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new store update request requires your attention.')
            ->line('Store: **' . $this->store->store_name . '** (' . $this->store->store_code . ')')
            ->line('Requested By: ' . $this->updateRequest->requester?->name . ' (' . ucfirst($this->updateRequest->requested_role) . ')')
            ->line($this->updateRequest->getChangesSummary() ? 
                    count($this->updateRequest->getChangesSummary()) . ' field(s) have been modified' : 
                    'No changes detected')
            ->action('Review Request', url(route('admin.store-updates.show', $this->updateRequest)))
            ->line('Please review and approve or reject within 48 hours.')
            ->salutation('Best Regards, Pharma System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'store_update_requested',
            'store_id' => $this->store->id,
            'store_name' => $this->store->store_name,
            'store_code' => $this->store->store_code,
            'update_request_id' => $this->updateRequest->id,
            'requested_by' => $this->updateRequest->requester?->name,
            'requested_role' => $this->updateRequest->requested_role,
            'requested_at' => $this->updateRequest->created_at,
            'changes_count' => count($this->updateRequest->getChangesSummary()),
        ];
    }
}
