<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MachineApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Routes untuk Mobile E-Commerce & Manufaktur
|--------------------------------------------------------------------------
*/

// Authentication (Public & SSO)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/sso', [AuthController::class, 'sso']);
Route::post('/sso', [AuthController::class, 'sso']);

// Catalog Mesin & Kategori (Public)
Route::get('/machines', [MachineApiController::class, 'index']);
Route::get('/machines/{id}', [MachineApiController::class, 'show']);
Route::get('/categories', [MachineApiController::class, 'categories']);

// Orders, Payments & Services (Accessible with or without Sanctum token for flexible dev)
Route::get('/orders', [OrderApiController::class, 'index']);
Route::post('/orders', [OrderApiController::class, 'store']);
Route::get('/orders/{id}', [OrderApiController::class, 'show']);
Route::post('/orders/{id}/pay-dp', [OrderApiController::class, 'uploadDpProof']);
Route::post('/orders/{id}/pay-pelunasan', [OrderApiController::class, 'uploadPelunasanProof']);
Route::get('/orders/{id}/invoice', [OrderApiController::class, 'invoice']);

// Log Semua Pembayaran (Payment History)
Route::get('/payments', [PaymentApiController::class, 'index']);

// Layanan Purna Jual (After-Sales Services)
Route::get('/services/bookings', [ServiceApiController::class, 'listBookings']);
Route::post('/services/bookings', [ServiceApiController::class, 'bookingService']);
Route::get('/services/warranty-claims', [ServiceApiController::class, 'listWarrantyClaims']);
Route::post('/services/warranty-claims', [ServiceApiController::class, 'submitWarrantyClaim']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});