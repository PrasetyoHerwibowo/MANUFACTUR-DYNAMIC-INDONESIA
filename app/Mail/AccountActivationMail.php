<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $activationCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@manufacturdynamic.co.id'),
                config('mail.from.name', 'PT Manufactur Dynamic Indonesia')
            ),
            subject: 'Kode Aktivasi Akun Anda: ' . $this->activationCode . ' - PT Manufactur Dynamic Indonesia',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account_activation',
            with: [
                'user' => $this->user,
                'activationCode' => $this->activationCode,
                'expiresInMinutes' => 1440, // 24 jam
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
