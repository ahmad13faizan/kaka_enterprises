<?php

namespace Agency\ProductSample\Notifications;

use Agency\ProductSample\Models\SampleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SampleRequestStatusChanged extends Notification
{
    use Queueable;

    public function __construct(protected SampleRequest $sampleRequest) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Sample Request Update — #' . $this->sampleRequest->id)
            ->line('Your sample request status: ' . ucfirst($this->sampleRequest->status));

        if ($this->sampleRequest->status === 'approved') {
            $message->line('Your sample has been approved and will be shipped soon.');
        }

        if ($this->sampleRequest->status === 'rejected') {
            $reason = $this->sampleRequest->rejection_reason ?: 'No reason provided.';
            $message->line('Reason: ' . $reason);
        }

        if ($this->sampleRequest->status === 'shipped') {
            $message->line('Your sample has been shipped!');
            if ($this->sampleRequest->tracking_info) {
                $message->line('Tracking: ' . $this->sampleRequest->tracking_info);
            }
        }

        if ($this->sampleRequest->status === 'delivered') {
            $message->line('Your sample has been delivered. Enjoy!');
        }

        return $message;
    }
}
