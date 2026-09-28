<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email pelanggan
    |--------------------------------------------------------------------------
    |
    | "dns" melakukan pencarian DNS (MX/A) untuk setiap alamat yang disimpan,
    | sehingga typos pada domain tertangkap. Biayanya: setiap penyimpanan
    | pelanggan bergantung pada koneksi internet, dan alamat di domain yang
    | belum punya record DNS (mis. domain internal atau baru) akan ditolak.
    |
    | Setel false pada instalasi lokal/tertutup yang tidak punya akses DNS —
    | validasi sintaks RFC tetap berjalan, hanya pengecekan MX/A yang dilewati.
    |
    */

    'verify_email_dns' => (bool) env('VALIDATE_EMAIL_DNS', true),

];
