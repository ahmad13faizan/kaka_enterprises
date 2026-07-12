<?php

namespace Agency\SiteVisit\Notifications;

use Agency\SiteVisit\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteVisitReassigned extends Notification
{
    use Queueable;

    public function __construct(
        protected SiteVisit $visit,
        protected string $role // 'assigned' or 'removed'
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->role === 'removed') {
            return (new MailMessage)
                ->subject('Site Visit Reassigned — #' . $this->visit->id)
                ->line('You have been removed from site visit #' . $this->visit->id . '.')
                ->line('This visit has been reassigned to another rep.');
        }

        return (new MailMessage)
            ->subject('Site Visit Assigned to You — #' . $this->visit->id)
            ->greeting('A site visit has been reassigned to you!')
            ->line('Customer: ' . $this->visit->customer->first_name . ' ' . $this->visit->customer->last_name)
            ->line('Address: ' . $this->visit->address)
            ->line('Preferred Date: ' . $this->visit->preferred_date->format('d M Y'))
            ->action('View Details', route('admin.site-visits.show', $this->visit->id));
    }
}
