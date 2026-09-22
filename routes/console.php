<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Penjadwalan pembayaran
|--------------------------------------------------------------------------
| Pembayaran yang melewati batas waktu otomatis menjadi gagal sehingga
| pelanggan harus mengulang pembayaran dari awal. Jalankan cron
| "php artisan schedule:run" setiap menit agar penjadwalan ini aktif.
*/
Schedule::command('payments:expire')
    ->everyTenMinutes()
    ->withoutOverlapping();

