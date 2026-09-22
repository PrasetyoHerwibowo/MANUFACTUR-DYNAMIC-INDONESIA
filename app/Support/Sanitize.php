<?php

namespace App\Support;

/**
 * Pembersih teks dari input formulir admin.
 *
 * Teks yang diketik admin dibersihkan dari tag HTML, entitas, dan karakter
 * kendali sebelum divalidasi/di-simpan sehingga tidak dapat dipakai untuk
 * HTML injection, XSS, maupun penyusupan karakter tak terlihat.
 */
class Sanitize
{
    /** Karakter kendali yang dibuang (tab dan baris baru tetap diizinkan). */
    private const CONTROL_CHARACTERS = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u';

    /** Spasi tidak biasa yang dinormalkan menjadi spasi biasa. */
    private const ODD_SPACES = '/[\x{00A0}\x{1680}\x{2000}-\x{200A}\x{202F}\x{205F}\x{3000}]/u';

    /** Karakter tak terlihat: zero width dan pengendali arah teks. */
    private const INVISIBLE_CHARACTERS = '/[\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2066}-\x{2069}\x{FEFF}]/u';

    /**
     * Ubah nilai menjadi teks biasa yang aman disimpan.
     *
     * - Tag HTML dibuang, termasuk tag yang disamarkan sebagai entitas HTML
     *   (mis. &lt;script&gt;) maupun yang bertumpuk (mis. &amp;lt;).
     * - Karakter kendali, zero width, dan pengendali arah teks dibuang.
     * - Spasi/baris baru berurutan dirapikan menjadi satu spasi.
     *
     * Panjang teks tidak dipotong di sini: batas panjang tetap divalidasi
     * agar admin menerima pesan kesalahan, bukan data yang terpotong diam-diam.
     */
    public static function text(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (! mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        $text = $value;

        // Beberapa putaran: tag dibuang, entitas diurai, lalu tag hasil uraian
        // (mis. &lt;b&gt;) dibuang pula pada putaran berikutnya.
        for ($i = 0; $i < 3; $i++) {
            $text = strip_tags($text);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $text = preg_replace(self::CONTROL_CHARACTERS, '', $text) ?? '';
        $text = preg_replace(self::ODD_SPACES, ' ', $text) ?? '';
        $text = preg_replace(self::INVISIBLE_CHARACTERS, '', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }
}
