<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use SerializesModels;

    public string $customerEmail;
    public string $userLocale;

    public function __construct(string $customerEmail, string $locale = 'ko')
    {
        $this->customerEmail = $customerEmail;
        $this->userLocale = $locale;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.welcome.subject', [], $this->userLocale),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            with: [
                'locale' => $this->userLocale,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
