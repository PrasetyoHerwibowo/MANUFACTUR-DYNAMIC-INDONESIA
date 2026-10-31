<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MachineApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes untuk Mobile App & E-Commerce Manufaktur
| Dilindungi Laravel Sanctum & Rate Limiting
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (Dapat diakses tanpa login)
// ==========================================

// Catalog Mesin & Kategori (Halaman Home Mobile App)
Route::get('/machines', [MachineApiController::class, 'index']);
Route::get('/machines/{id}', [MachineApiController::class, 'show']);
Route::get('/categories', [MachineApiController::class, 'categories']);

// Autentikasi Pengguna & Aktivasi Akun (Rate Limited untuk mencegah brute force)
Route::middleware('throttle:15,1')->group(function () {
    // Registrasi Akun Baru (Kirim 10 digit kode acak ke email)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Aktivasi Akun (Memasukkan 10 digit kode verifikasi acak dari email)
    Route::post('/auth/verify-activation', [AuthController::class, 'verifyActivation']);
    Route::post('/verify-activation', [AuthController::class, 'verifyActivation']);

    // Kirim Ulang Kode Aktivasi 10 Digit
    Route::post('/auth/resend-code', [AuthController::class, 'resendActivationCode']);
    Route::post('/resend-code', [AuthController::class, 'resendActivationCode']);

    // Login Akun Biasa (Validasi password ketat & cek aktivasi akun)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Login & Registrasi via Google OAuth
    Route::post('/auth/google', [AuthController::class, 'googleAuth']);
    Route::post('/google', [AuthController::class, 'googleAuth']);
    Route::post('/auth/sso', [AuthController::class, 'sso']);
    Route::post('/sso', [AuthController::class, 'sso']);

    // Pemulihan Kata Sandi / Reset Password via Kode Acak 10 Digit ke Email
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/verify-reset-code', [AuthController::class, 'verifyResetCode']);
    Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// ==========================================================
// 2. PROTECTED ROUTES (Wajib Login & Menggunakan Sanctum Bearer Token)
// Halaman Pesanan, Pembayaran, Servis & Garansi
// ==========================================================
Route::middleware('auth:sanctum')->group(function () {
    // Sesi & Profil Pengguna
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Pesanan / Purchase Order (Page Pesanan di Mobile App)
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::get('/orders/{id}', [OrderApiController::class, 'show']);
    Route::post('/orders/{id}/pay-dp', [OrderApiController::class, 'uploadDpProof']);
    Route::post('/orders/{id}/pay-pelunasan', [OrderApiController::class, 'uploadPelunasanProof']);
    Route::get('/orders/{id}/invoice', [OrderApiController::class, 'invoice']);

    // Log Transaksi Pembayaran
    Route::get('/payments', [PaymentApiController::class, 'index']);

    // Layanan Servis & Garansi (Page Servis di Mobile App)
    Route::get('/services/bookings', [ServiceApiController::class, 'listBookings']);
    Route::post('/services/bookings', [ServiceApiController::class, 'bookingService']);
    Route::get('/services/warranty-claims', [ServiceApiController::class, 'listWarrantyClaims']);
    Route::post('/services/warranty-claims', [ServiceApiController::class, 'submitWarrantyClaim']);
});