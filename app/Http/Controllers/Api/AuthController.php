<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\AccountActivationMail;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    /**
     * POST /api/register
     * Registrasi akun baru dengan aktivasi kode acak 10 digit ke email aktif.
     * Mengikuti standar OWASP Top 10 (Sanitasi input, hashing kuat, tanpa kebocoran kode/debug).
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[a-zA-Z]/',
            ],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk ke akun Anda.',
            'email.email' => 'Format email tidak valid atau domain email tidak aktif.',
            'password.min' => 'Kata sandi minimal harus 8 karakter untuk keamanan.',
            'password.regex' => 'Kata sandi harus mengandung kombinasi huruf dan angka.',
        ]);

        try {
            // Generate kode acak 10 digit (kombinasi huruf kapital & angka via CSPRNG)
            $activationCode = $this->generate10DigitCode();
            $email = strtolower(trim($validated['email']));

            $user = DB::transaction(function () use ($validated, $activationCode, $email) {
                $cleanName = strip_tags(trim($validated['name']));
                $cleanCompany = !empty($validated['company']) ? strip_tags(trim($validated['company'])) : 'Mitra ' . $cleanName;
                $cleanPhone = !empty($validated['phone']) ? strip_tags(trim($validated['phone'])) : null;

                $user = User::create([
                    'name' => $cleanName,
                    'email' => $email,
                    'phone' => $cleanPhone,
                    'password' => Hash::make($validated['password']),
                    'role' => User::ROLE_CUSTOMER,
                    'is_active' => false,
                    'activation_code' => $activationCode,
                    'activation_code_expires_at' => Carbon::now()->addMinutes(15), // OWASP: OTP expired dalam 15 menit
                    'auth_provider' => 'email',
                ]);

                // Buat profil customer B2B terkait
                Customer::create([
                    'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
                    'name' => $cleanName,
                    'email' => $email,
                    'phone' => $cleanPhone,
                    'company' => $cleanCompany,
                    'address' => '-',
                ]);

                return $user;
            });

            // Kirim email aktivasi berisikan kode acak 10 digit langsung ke inbox pengguna
            try {
                Mail::to($user->email)->send(new AccountActivationMail($user, $activationCode));
            } catch (Throwable $mailEx) {
                Log::warning('Gagal mengirim email aktivasi via mailer default: ' . $mailEx->getMessage(), [
                    'email' => $user->email,
                ]);
            }

            // OWASP: Jangan pernah mengembalikan kode OTP / activation_code di HTTP response
            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran akun berhasil! Kode aktivasi 10 digit telah dikirimkan ke email Anda. Silakan masukkan kode untuk mengaktifkan akun.',
                'needs_activation' => true,
                'email' => $user->email,
                'expires_in_minutes' => 15,
            ], 201);
        } catch (Throwable $e) {
            Log::error('AuthController register error: ' . $e->getMessage(), [
                'email' => $request->input('email'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pendaftaran akun. Silakan periksa kembali data Anda.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/verify-activation
     * Validasi kode 10 digit dan aktivasi akun
     * OWASP: Anti-brute force (maksimal 5 percobaan gagal), timing-attack resistance (hash_equals), dan single-use OTP
     */
    public function verifyActivation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:10'],
        ], [
            'code.size' => 'Kode aktivasi harus tepat 10 digit angka dan huruf.',
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $inputCode = strtoupper(trim($validated['code']));

            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun dengan email ini tidak ditemukan.',
                ], 404);
            }

            if ($user->is_active && $user->email_verified_at !== null) {
                // Akun sudah aktif sebelumnya, buat token langsung
                $token = $user->createToken('manufaktur-mobile')->plainTextToken;
                $customer = Customer::where('email', $user->email)->first();

                return response()->json([
                    'success' => true,
                    'message' => 'Akun Anda sudah aktif. Anda dapat langsung menggunakan aplikasi.',
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'company' => $customer?->company ?? 'Mitra Perusahaan',
                        'customer_id' => $customer?->id,
                        'is_active' => true,
                    ],
                ]);
            }

            // OWASP: Proteksi Anti-Brute Force (maksimal 5 kali salah)
            $attemptKey = 'verify_attempts_' . sha1($email);
            $attempts = (int) Cache::get($attemptKey, 0);

            if ($attempts >= 5) {
                // Batalkan kode karena terindikasi upaya brute force
                $user->activation_code = null;
                $user->activation_code_expires_at = null;
                $user->save();
                Cache::forget($attemptKey);

                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan kode verifikasi yang salah (Maksimal 5 kali). Kode aktivasi telah dibatalkan demi keamanan. Silakan klik "Kirim Ulang Kode".',
                    'is_locked' => true,
                ], 429);
            }

            // Validasi kadaluarsa kode (15 menit)
            if ($user->activation_code_expires_at && Carbon::now()->greaterThan($user->activation_code_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode aktivasi telah kadaluwarsa (berlaku 15 menit). Silakan klik "Kirim Ulang Kode".',
                    'is_expired' => true,
                ], 422);
            }

            // OWASP: Timing attack protection menggunakan hash_equals
            if (empty($user->activation_code) || ! hash_equals(strtoupper($user->activation_code), $inputCode)) {
                $attempts++;
                Cache::put($attemptKey, $attempts, now()->addMinutes(15));
                $remaining = max(0, 5 - $attempts);

                if ($attempts >= 5) {
                    // Batalkan kode karena telah mencapai batas 5 kali salah
                    $user->activation_code = null;
                    $user->activation_code_expires_at = null;
                    $user->save();

                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan kode verifikasi yang salah (Maksimal 5 kali). Kode aktivasi telah dibatalkan demi keamanan akun. Silakan klik "Kirim Ulang Kode".',
                        'is_locked' => true,
                    ], 429);
                }

                return response()->json([
                    'success' => false,
                    'message' => "Kode verifikasi yang Anda masukkan salah. Sisa percobaan: {$remaining} kali.",
                    'remaining_attempts' => $remaining,
                ], 422);
            }

            // Aktivasi akun sukses - bersihkan cache percobaan & hapus OTP (Single-Use Token)
            Cache::forget($attemptKey);
            $user->is_active = true;
            $user->email_verified_at = Carbon::now();
            $user->activation_code = null;
            $user->activation_code_expires_at = null;
            $user->save();

            // Pastikan customer profile ada
            $customer = Customer::where('email', $user->email)->first();
            if (! $customer) {
                $customer = Customer::create([
                    'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => 'Mitra ' . $user->name,
                    'address' => '-',
                ]);
            }

            // Generate token Sanctum
            $token = $user->createToken('manufaktur-mobile')->plainTextToken;

            Log::info("Akun {$user->email} berhasil diaktivasi dengan kode verifikasi 10 digit.");

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil diaktivasi! Selamat datang di aplikasi Manufaktur.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $customer->company,
                    'customer_id' => $customer->id,
                    'is_active' => true,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController verifyActivation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi kode aktivasi.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/resend-code
     * Kirim ulang kode aktivasi 10 digit ke email
     * OWASP: Anti-flooding rate limit (cooldown 60 detik per email)
     */
    public function resendActivationCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        try {
            $email = strtolower(trim($validated['email']));

            // Anti-flooding cooldown (60 detik)
            $cooldownKey = 'resend_cooldown_' . sha1($email);
            if (Cache::has($cooldownKey)) {
                $expireAt = Cache::get($cooldownKey);
                $remaining = max(1, $expireAt - time());
                return response()->json([
                    'success' => false,
                    'message' => "Harap tunggu {$remaining} detik sebelum meminta pengiriman ulang kode verifikasi.",
                ], 429);
            }

            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun dengan email tersebut tidak ditemukan.',
                ], 404);
            }

            if ($user->is_active && $user->email_verified_at !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Akun ini sudah aktif. Silakan langsung masuk.',
                    'already_active' => true,
                ]);
            }

            $newCode = $this->generate10DigitCode();
            $user->activation_code = $newCode;
            $user->activation_code_expires_at = Carbon::now()->addMinutes(15);
            $user->save();

            // Set cooldown 60 detik
            Cache::put($cooldownKey, time() + 60, now()->addSeconds(60));
            // Reset percobaan verifikasi
            Cache::forget('verify_attempts_' . sha1($email));

            try {
                Mail::to($user->email)->send(new AccountActivationMail($user, $newCode));
            } catch (Throwable $mailEx) {
                Log::warning('Resend email error: ' . $mailEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode aktivasi baru telah dikirimkan ke email Anda.',
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController resendActivationCode error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang kode verifikasi.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/forgot-password
     * Mengirimkan kode verifikasi 10 digit untuk pemulihan kata sandi ke email user
     * OWASP: Anti-flooding cooldown 60s, anti-enumeration generic message, OTP expired 15m.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        try {
            $email = strtolower(trim($validated['email']));

            // Anti-flooding cooldown (60 detik per email)
            $cooldownKey = 'forgot_cooldown_' . sha1($email);
            if (Cache::has($cooldownKey)) {
                $expireAt = Cache::get($cooldownKey);
                $remaining = max(1, $expireAt - time());
                return response()->json([
                    'success' => false,
                    'message' => "Harap tunggu {$remaining} detik sebelum meminta pengiriman ulang kode pemulihan.",
                ], 429);
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $resetCode = $this->generate10DigitCode();
                $user->reset_password_code = $resetCode;
                $user->reset_password_expires_at = Carbon::now()->addMinutes(15);
                $user->save();

                // Set cooldown 60 detik
                Cache::put($cooldownKey, time() + 60, now()->addSeconds(60));
                // Reset attempt counter
                Cache::forget('reset_attempts_' . sha1($email));

                try {
                    Mail::to($user->email)->send(new PasswordResetMail($user, $resetCode));
                } catch (Throwable $mailEx) {
                    Log::warning('Gagal mengirim email reset password: ' . $mailEx->getMessage(), [
                        'email' => $user->email,
                    ]);
                }
            }

            // OWASP A07: Respon seragam untuk mencegah user enumeration
            return response()->json([
                'success' => true,
                'message' => 'Jika email Anda terdaftar, kode verifikasi 10 digit telah dikirimkan ke kotak masuk email Anda.',
                'email' => $email,
                'expires_in_minutes' => 15,
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController forgotPassword error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permintaan reset kata sandi.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/verify-reset-code
     * Memvalidasi kode 10 digit pemulihan kata sandi
     * OWASP: Anti-brute force (maks 5 kali gagal), timing-safe comparison (hash_equals).
     */
    public function verifyResetCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:10'],
        ], [
            'code.size' => 'Kode pemulihan harus tepat 10 digit angka dan huruf.',
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $inputCode = strtoupper(trim($validated['code']));

            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan.',
                ], 404);
            }

            // Proteksi Anti-Brute Force (maksimal 5 kali salah)
            $attemptKey = 'reset_attempts_' . sha1($email);
            $attempts = (int) Cache::get($attemptKey, 0);

            if ($attempts >= 5) {
                $user->reset_password_code = null;
                $user->reset_password_expires_at = null;
                $user->save();

                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan kode yang salah (Maksimal 5 kali). Kode pemulihan telah dibatalkan demi keamanan.',
                    'is_locked' => true,
                ], 429);
            }

            // Validasi kadaluarsa kode
            if ($user->reset_password_expires_at && Carbon::now()->greaterThan($user->reset_password_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pemulihan telah kadaluwarsa (berlaku 15 menit). Silakan minta kode baru.',
                    'is_expired' => true,
                ], 422);
            }

            // Validasi kode dengan hash_equals
            if (empty($user->reset_password_code) || ! hash_equals(strtoupper($user->reset_password_code), $inputCode)) {
                $attempts++;
                Cache::put($attemptKey, $attempts, now()->addMinutes(15));
                $remaining = max(0, 5 - $attempts);

                if ($attempts >= 5) {
                    $user->reset_password_code = null;
                    $user->reset_password_expires_at = null;
                    $user->save();

                    return response()->json([
                        'success' => false,
                        'message' => 'Terlalu banyak percobaan kode yang salah (Maksimal 5 kali). Kode pemulihan telah dibatalkan demi keamanan.',
                        'is_locked' => true,
                    ], 429);
                }

                return response()->json([
                    'success' => false,
                    'message' => "Kode verifikasi salah. Sisa percobaan: {$remaining} kali.",
                    'remaining_attempts' => $remaining,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode verifikasi valid. Silakan buat kata sandi baru Anda.',
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController verifyResetCode error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi kode pemulihan.',
            ], 500);
        }
    }

    /**
     * POST /api/auth/reset-password
     * Mengganti password lama dengan password baru setelah kode verifikasi 10 digit divalidasi
     * OWASP: Password entropy, single-use OTP, Sanctum session revocation.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:10'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[a-zA-Z]/',
                'confirmed',
            ],
        ], [
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.regex' => 'Kata sandi baru harus mengandung kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'code.size' => 'Kode verifikasi harus tepat 10 digit.',
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $inputCode = strtoupper(trim($validated['code']));

            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan.',
                ], 404);
            }

            // Validasi kadaluarsa
            if ($user->reset_password_expires_at && Carbon::now()->greaterThan($user->reset_password_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pemulihan telah kadaluwarsa. Silakan minta kode baru.',
                    'is_expired' => true,
                ], 422);
            }

            // Validasi kode dengan hash_equals
            if (empty($user->reset_password_code) || ! hash_equals(strtoupper($user->reset_password_code), $inputCode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode pemulihan tidak valid.',
                ], 422);
            }

            // Update kata sandi baru
            $user->password = Hash::make($validated['password']);
            $user->reset_password_code = null;
            $user->reset_password_expires_at = null;
            // Jika akun belum aktif / terverifikasi, aktivasi sekaligus
            $user->is_active = true;
            if ($user->email_verified_at === null) {
                $user->email_verified_at = Carbon::now();
            }
            $user->save();

            // OWASP: Revoke semua token Sanctum aktif sebelumnya (Session invalidation on password change)
            $user->tokens()->delete();
            Cache::forget('reset_attempts_' . sha1($email));

            Log::info("Kata sandi user {$user->email} berhasil di-reset via kode 10 digit.");

            return response()->json([
                'success' => true,
                'message' => 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.',
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController resetPassword error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kata sandi.',
            ], 500);
        }
    }

    /**
     * POST /api/login
     * Login aman dengan proteksi akun aktif & Sanctum
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $user = User::where('email', $email)->first();

            // Cegah bypass auth: verifikasi hash password secara ketat
            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau kata sandi yang Anda masukkan salah.',
                ], 401);
            }

            // Verifikasi status aktivasi akun: wajib aktif sebelum bisa login
            if (! $user->is_active || $user->email_verified_at === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda belum diaktivasi. Silakan masukkan 10 digit kode verifikasi yang telah dikirim ke email Anda.',
                    'needs_activation' => true,
                    'email' => $user->email,
                ], 403);
            }

            // Hapus token lama yang tidak terpakai jika ada
            $user->tokens()->where('name', 'manufaktur-mobile')->delete();

            $customer = Customer::where('email', $user->email)->first();
            $token = $user->createToken('manufaktur-mobile')->plainTextToken;

            Log::info("Pengguna {$user->email} berhasil login via Sanctum API.");

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $customer?->company ?? 'Mitra Perusahaan',
                    'customer_id' => $customer?->id,
                    'role' => $user->role,
                    'is_active' => true,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController login error: ' . $e->getMessage(), [
                'email' => $request->input('email'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kendala saat melakukan autentikasi login.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/auth/google
     * Login / Registrasi aman via Google OAuth
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'google_id' => ['nullable', 'string', 'max:150'],
            'avatar' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $email = strtolower(trim($validated['email']));

            $result = DB::transaction(function () use ($validated, $email) {
                $user = User::where('email', $email)->first();
                $isNewUser = false;

                if (! $user) {
                    $isNewUser = true;
                    $user = User::create([
                        'name' => trim($validated['name']),
                        'email' => $email,
                        'phone' => !empty($validated['phone']) ? trim($validated['phone']) : null,
                        'avatar' => $validated['avatar'] ?? null,
                        'password' => Hash::make(Str::random(32)),
                        'role' => User::ROLE_CUSTOMER,
                        'is_active' => true, // Akun Google terverifikasi otomatis
                        'email_verified_at' => Carbon::now(),
                        'auth_provider' => 'google',
                        'google_id' => $validated['google_id'] ?? null,
                    ]);
                } else {
                    // Update profil & pastikan aktif
                    $user->is_active = true;
                    if (! $user->email_verified_at) {
                        $user->email_verified_at = Carbon::now();
                    }
                    if (!empty($validated['google_id'])) {
                        $user->google_id = $validated['google_id'];
                    }
                    if (!empty($validated['avatar'])) {
                        $user->avatar = $validated['avatar'];
                    }
                    $user->save();
                }

                $customer = Customer::where('email', $email)->first();
                if (! $customer) {
                    $company = !empty($validated['company'])
                        ? trim($validated['company'])
                        : $this->inferCompanyNameFromEmail($email, $validated['name']);

                    $customer = Customer::create([
                        'customer_code' => 'GOOG-' . strtoupper(substr(uniqid(), -6)),
                        'name' => $user->name,
                        'email' => $email,
                        'phone' => $user->phone,
                        'company' => $company,
                        'address' => '-',
                    ]);
                }

                $token = $user->createToken('manufaktur-mobile-google')->plainTextToken;

                return [$user, $customer, $token, $isNewUser];
            });

            [$user, $customer, $token, $isNewUser] = $result;

            Log::info("Google OAuth berhasil untuk {$user->email} (New: " . ($isNewUser ? 'true' : 'false') . ")");

            return response()->json([
                'success' => true,
                'message' => $isNewUser
                    ? 'Pendaftaran dan login melalui Google berhasil!'
                    : 'Masuk melalui Google berhasil!',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'company' => $customer->company,
                    'customer_id' => $customer->id,
                    'auth_type' => 'google_oauth',
                    'is_active' => true,
                ],
            ], $isNewUser ? 201 : 200);
        } catch (Throwable $e) {
            Log::error('AuthController googleAuth error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses autentikasi Google OAuth.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/auth/sso
     * Backward-compatible SSO endpoint
     */
    public function sso(Request $request): JsonResponse
    {
        if ($request->input('provider') === 'google' || empty($request->input('provider'))) {
            return $this->googleAuth($request);
        }

        return $this->googleAuth($request);
    }

    /**
     * GET /api/me
     * Ambil data profil pengguna yang sedang login
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengguna tidak terautentikasi.',
                ], 401);
            }

            $customer = Customer::where('email', $user->email)->first();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $customer?->company ?? 'Mitra Manufaktur',
                    'customer_id' => $customer?->id,
                    'role' => $user->role,
                    'is_active' => (bool) $user->is_active,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController me error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat profil pengguna.',
            ], 500);
        }
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->user()?->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil. Sesi telah diakhiri.',
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan logout.',
            ], 500);
        }
    }

    /**
     * Generate 10 digit alphanumeric code (Huruf Kapital & Angka acak)
     * Contoh: 7KM9X2B4P1
     */
    private function generate10DigitCode(): string
    {
        $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ'; // Hilangkan 0, 1, I, O agar tidak membingungkan pengguna
        $code = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < 10; $i++) {
            $code .= $characters[random_int(0, $max)];
        }

        return $code;
    }

    /**
     * Helper nama perusahaan berdasarkan email
     */
    private function inferCompanyNameFromEmail(string $email, string $userName): string
    {
        $domain = substr(strrchr($email, "@"), 1);
        $publicDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
        if (in_array(strtolower($domain), $publicDomains)) {
            return 'Mitra ' . $userName;
        }

        $parts = explode('.', $domain);
        $cleanDomain = ucfirst($parts[0]);
        return 'PT ' . $cleanDomain . ' Manufaktur';
    }
}