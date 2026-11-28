<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memastikan request API Sanctum hanya dapat diakses oleh akun Customer B2B.
 * Akun Administrator diisolasi dan dilarang menggunakan API mobile demi keamanan.
 */
class EnsureApiIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi autentikasi tidak valid atau telah kedaluwarsa. Silakan login kembali.',
            ], 401);
        }

        // Isolasi keamanan: Tolak akses jika user memiliki role admin
        if ($user->role === User::ROLE_ADMIN || $user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Endpoint mobile ini hanya untuk akun Customer B2B. Administrator wajib mengelola sistem melalui Web Panel Admin.',
            ], 403);
        }

        return $next($request);
    }
}

