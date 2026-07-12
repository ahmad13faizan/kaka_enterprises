<?php

namespace Agency\SiteVisit\Notifications;

use Agency\SiteVisit\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteVisitAssigned extends Notification
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
            ->subject('Site Visit Assigned to You — #' . $this->visit->id)
            ->greeting('You have been assigned a site visit!')
            ->line('Customer: ' . $this->visit->customer->first_name . ' ' . $this->visit->customer->last_name)
            ->line('Product: ' . ($this->visit->product->name ?? 'N/A'))
            ->line('Address: ' . $this->visit->address)
            ->line('Preferred Date: ' . $this->visit->preferred_date->format('d M Y'))
            ->line('Time Slot: ' . ucfirst($this->visit->preferred_time_slot))
            ->when($this->visit->notes, fn ($msg) => $msg->line('Notes: ' . $this->visit->notes))
            ->action('View Details', route('admin.site-visits.show', $this->visit->id));
    }
}
