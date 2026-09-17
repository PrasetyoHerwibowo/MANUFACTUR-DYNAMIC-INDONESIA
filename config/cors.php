<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi CORS
|--------------------------------------------------------------------------
|
| Dibutuhkan kalau aplikasi Flutter dijalankan sebagai Flutter Web
| (`flutter run -d chrome`) atau kalau frontend lain memanggil API ini
| dari domain/port yang berbeda. Aplikasi Flutter Android/desktop tidak
| butuh CORS, tapi tidak ada salahnya diaktifkan untuk development.
|
| Nilai di bawah hanya untuk DEVELOPMENT. Untuk produksi, ganti
| allowed_origins menjadi daftar domain yang benar-benar dipakai.
|
*/

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
