<?php

namespace Agency\SiteVisit\Notifications;

use Agency\SiteVisit\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteVisitRequested extends Notification
{
    use Queueable;

    public function __construct(
        protected SiteVisit $visit
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Site Visit Request #' . $this->visit->id)
            ->greeting('New site visit request received!')
            ->line('Customer: ' . $this->visit->customer->first_name . ' ' . $this->visit->customer->last_name)
            ->line('Product: ' . ($this->visit->product->name ?? 'N/A'))
            ->line('Address: ' . $this->visit->address)
            ->line('Preferred Date: ' . $this->visit->preferred_date->format('d M Y'))
            ->line('Time Slot: ' . ucfirst($this->visit->preferred_time_slot))
            ->action('View Request', route('admin.site-visits.show', $this->visit->id));
    }
}
