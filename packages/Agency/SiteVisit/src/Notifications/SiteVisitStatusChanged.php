<?php

namespace Agency\SiteVisit\Notifications;

use Agency\SiteVisit\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteVisitStatusChanged extends Notification
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
        $message = (new MailMessage)
            ->subject('Site Visit Update — #' . $this->visit->id)
            ->greeting('Your site visit request has been updated!')
            ->line('Status: ' . ucfirst($this->visit->status));

        if ($this->visit->status === 'scheduled') {
            $message->line('Scheduled for: ' . $this->visit->scheduled_date->format('d M Y') . ' at ' . $this->visit->scheduled_time);
        }

        if ($this->visit->status === 'cancelled') {
            $message->line('Your site visit request has been cancelled.');
        }

        if ($this->visit->status === 'completed') {
            $message->line('Your site visit has been completed. Thank you!');
        }

        return $message;
    }
}
