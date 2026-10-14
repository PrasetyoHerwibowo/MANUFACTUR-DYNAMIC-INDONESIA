<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // Tamu yang mencoba masuk halaman terproteksi diarahkan ke login admin.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Semua request ke /api/* selalu dibalas JSON (termasuk validation error),
        // supaya Flutter bisa memproses error tanpa perlu header khusus.
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        // 1. Penanganan khusus error koneksi & query database (Supabase PostgreSQL / PDO)
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                \Illuminate\Support\Facades\Log::error('API Database Error: ' . $e->getMessage(), [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kendala pada koneksi atau operasi basis data.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Database service error.',
                ], 500);
            }

            if ($request->is('admin*') && ! config('app.debug')) {
                \Illuminate\Support\Facades\Log::error('Admin Database Error: ' . $e->getMessage(), [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                ]);

                return back()->withInput()->with('error', 'Terjadi kendala pada koneksi basis data. Silakan coba sesaat lagi.');
            }
        });

        // 2. Penanganan autentikasi token / sesi
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi autentikasi tidak valid atau telah kedaluwarsa. Silakan login kembali.',
                ], 401);
            }
        });

        // 3. Penanganan record model tidak ditemukan
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang diminta tidak ditemukan.',
                ], 404);
            }
        });

        // 4. Penanganan route tidak ditemukan (404)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint API tidak ditemukan.',
                ], 404);
            }
        });

        // 5. Penanganan validasi input form
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // 6. Penanganan umum unhandled exception pada API
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                \Illuminate\Support\Facades\Log::error('API Unhandled Exception: ' . $e->getMessage(), [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses permintaan Anda.',
                    'error' => config('app.debug') ? $e->getMessage() : 'Internal server error.',
                ], 500);
            }
        });
    })->create();
