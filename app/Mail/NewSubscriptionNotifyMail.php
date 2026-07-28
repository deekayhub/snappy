<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubscriptionNotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $planName,
        public string $amount,
        public string $billingPeriod,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Subscription – ' . $this->user->name . ' (' . $this->planName . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-subscription-notify',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
