<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Quote Has Been Rejected',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-rejected',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
