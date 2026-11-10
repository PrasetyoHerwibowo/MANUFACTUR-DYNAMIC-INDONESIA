<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $resetCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'ayamoportesting@gmail.com'),
                config('mail.from.name', 'PT Manufactur Dynamic Indonesia')
            ),
            subject: 'Kode Pemulihan Kata Sandi Anda: ' . $this->resetCode . ' - PT Manufactur Dynamic Indonesia',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_reset',
            with: [
                'user' => $this->user,
                'resetCode' => $this->resetCode,
                'expiresInMinutes' => 15, // 15 menit sesuai OWASP
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
