<?php

namespace Agency\ProductSample\Notifications;

use Agency\ProductSample\Models\SampleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SampleRequestReceived extends Notification
{
    use Queueable;

    public function __construct(protected SampleRequest $sampleRequest) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Sample Request #' . $this->sampleRequest->id)
            ->line('Customer: ' . $this->sampleRequest->customer->first_name . ' ' . $this->sampleRequest->customer->last_name)
            ->line('Product: ' . ($this->sampleRequest->product->name ?? 'N/A'))
            ->line('Quantity: ' . $this->sampleRequest->quantity)
            ->action('View Request', route('admin.sample-requests.show', $this->sampleRequest->id));
    }
}
