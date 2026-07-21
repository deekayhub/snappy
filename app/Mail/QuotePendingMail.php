<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotePendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quote Has Been Marked as Pending',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-pending',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
