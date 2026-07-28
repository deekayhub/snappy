<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRegisteredNotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $role)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New ' . ucfirst($this->role) . ' Registered – ' . $this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-user-registered-notify',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
