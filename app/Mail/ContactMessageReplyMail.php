<?php

namespace App\Mail;

use App\Models\ContactMessageReply;
use App\Support\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Balasan pesan formulir kontak yang dikirim dari server lewat Gmail SMTP.
 *
 * Berbeda dari tautan "mailto:", email ini benar-benar keluar dari aplikasi
 * sehingga alamat pengirim mengikuti akun Gmail yang dikonfigurasi di .env
 * dan riwayatnya tersimpan di tabel contact_message_replies.
 */
class ContactMessageReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessageReply $reply) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reply->subject,
        );
    }

    public function content(): Content
    {
        $company = Site::company();

        return new Content(
            view: 'mail.contact-reply',
            with: [
                'body' => $this->reply->body,
                'companyName' => $company->name ?: 'Manufactur Dynamic Indonesia',
                'companyTagline' => $company->tagline,
            ],
        );
    }
}
