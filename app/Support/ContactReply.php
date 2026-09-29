<?php

namespace App\Support;

use App\Models\ContactMessage;

/**
 * Penyusun teks balasan default untuk pesan formulir kontak.
 *
 * Dipakai dua tempat: editor di panel admin (sebagai isi awal textarea)
 * dan Mailable yang mengirim lewat Gmail SMTP. Keduanya memanggil kelas
 * ini agar isi yang dilihat admin persis sama dengan yang terkirim.
 */
class ContactReply
{
    /** Panjang maksimum subjek balasan. */
    public const SUBJECT_MAX = 200;

    /** Panjang maksimum isi balasan. */
    public const BODY_MAX = 5000;

    /** Jumlah baris pesan pengunjung yang ikut dikutip. */
    private const QUOTE_LINES = 6;

    /**
     * Subjek balasan: "Re: + subjek pesan", dengan teksCadangan bila kosong.
     */
    public static function subject(string $originalSubject): string
    {
        $subject = trim($originalSubject);

        return 'Re: '.($subject !== '' ? $subject : 'Permintaan informasi & penawaran');
    }

    /**
     * Isi balasan default berupa surat singkat yang menyertakan kutipan
     * pesan pengunjung dan data kontak perusahaan.
     */
    public static function body(ContactMessage $message): string
    {
        $company = Site::company();
        $companyName = $company->name ?: 'Manufactur Dynamic Indonesia';

        $recipient = $message->name.($message->company ? ' ('.$message->company.')' : '');

        $quoted = [];
        foreach (array_slice(
            preg_split('/\r\n|\r|\n/', trim((string) $message->message)),
            -self::QUOTE_LINES
        ) as $line) {
            $quoted[] = '> '.trim($line);
        }

        $lines = [
            'Halo Bapak/Ibu '.$recipient.',',
            '',
            'Terima kasih sudah menghubungi '.$companyName.'. '
                .'Kami sudah menerima pesan Bapak/Ibu pada '
                .$message->created_at->translatedFormat('d F Y').' dan sedang kami bahas di internal.',
            '',
            '--------- Pesan Bapak/Ibu ---------',
        ];

        foreach ($quoted as $line) {
            $lines[] = $line;
        }

        $lines[] = '--------------------------------------';
        $lines[] = '';
        $lines[] = 'TIM kami akan menghubungi Bapak/Ibu kembali dalam 1-2 hari kerja untuk '
            .'membahas lebih lanjut. Kalau ada yang ingin ditanyakan meantime, '
            .'Bapak/Ibu bisa membalas surel ini atau menghubungi kami lewat telepon '
            .'dan WhatsApp di bawah.';
        $lines[] = '';
        $lines[] = 'Sekali lagi terima kasih atas ketertarikan Bapak/Ibu. Kami menunggu kabar '
            .'dari Bapak/Ibu.';
        $lines[] = '';
        $lines[] = 'Salam,';
        $lines[] = 'Tim '.$companyName;

        foreach ($company->contactDetails() as $label => $value) {
            $lines[] = $label.': '.$value;
        }

        return implode("\n", $lines);
    }
}
