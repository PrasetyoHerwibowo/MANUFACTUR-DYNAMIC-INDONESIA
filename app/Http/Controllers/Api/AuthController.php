<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($validated['password']),
                ]);

                // Auto create or associate Customer profile
                $customer = Customer::create([
                    'customer_code' => 'CUST-' . strtoupper(substr(uniqid(), -6)),
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => !empty($validated['phone']) ? $validated['phone'] : null,
                    'company' => $validated['company'] ?? 'Perusahaan Kopi / Mitra',
                    'address' => '-',
                ]);

                $token = $user->createToken('manufaktur-mobile')->plainTextToken;

                return [$user, $customer, $token];
            });

            [$user, $customer, $token] = $result;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi akun berhasil',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $customer->company,
                    'customer_id' => $customer->id,
                ],
            ], 201);
        } catch (Throwable $e) {
            Log::error('AuthController register error: ' . $e->getMessage(), [
                'email' => $validated['email'],
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan pendaftaran akun. Silakan coba lagi nanti.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $user = User::where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password yang Anda masukkan salah',
                ], 401);
            }

            $customer = Customer::where('email', $user->email)->first();

            $token = $user->createToken('manufaktur-mobile')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company' => $customer?->company ?? 'PT Kopi Nusantara Jaya',
                    'customer_id' => $customer?->id,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController login error: ' . $e->getMessage(), [
                'email' => $validated['email'],
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat masuk akun.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/auth/sso
     * Single Sign-On (Google Workspace, Microsoft Entra ID / 365, Apple)
     */
    public function sso(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'in:google,microsoft,apple,sso'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'string'],
            'provider_id' => ['nullable', 'string'],
        ]);

        try {
            $email = strtolower(trim($validated['email']));
            $providerName = ucfirst($validated['provider']);

            $result = DB::transaction(function () use ($validated, $email, $providerName) {
                $user = User::where('email', $email)->first();
                $isNewUser = false;

                if (! $user) {
                    $isNewUser = true;
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $email,
                        'phone' => $validated['phone'] ?? null,
                        'avatar' => $validated['avatar'] ?? null,
                        'password' => Hash::make(Str::random(32)),
                    ]);
                }

                // Find or create customer corporate profile
                $customer = Customer::where('email', $email)->first();
                if (! $customer) {
                    $companyName = ! empty($validated['company']) 
                        ? $validated['company'] 
                        : $this->inferCompanyNameFromEmail($email, $validated['name']);

                    $customer = Customer::create([
                        'customer_code' => 'SSO-' . strtoupper(substr(uniqid(), -6)),
                        'name' => $validated['name'],
                        'email' => $email,
                        'phone' => !empty($validated['phone']) ? $validated['phone'] : null,
                        'company' => $companyName,
                        'address' => '-',
                    ]);
                }

                $token = $user->createToken('manufaktur-mobile-sso')->plainTextToken;

                return [$user, $customer, $token, $isNewUser];
            });

            [$user, $customer, $token, $isNewUser] = $result;

            return response()->json([
                'success' => true,
                'message' => $isNewUser 
                    ? "Registrasi & login via $providerName SSO berhasil" 
                    : "Login via $providerName SSO berhasil",
                'is_new_user' => $isNewUser,
                'provider' => $validated['provider'],
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'company' => $customer->company,
                    'customer_id' => $customer->id,
                    'auth_type' => 'sso',
                    'sso_provider' => $validated['provider'],
                ],
            ], $isNewUser ? 201 : 200);
        } catch (Throwable $e) {
            Log::error('AuthController SSO error: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses Single Sign-On (SSO). Silakan coba kembali.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Infer company name if not supplied
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

    /**
     * GET /api/me
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
                    'company' => $customer?->company ?? 'PT Kopi Nusantara Jaya',
                    'customer_id' => $customer?->id,
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
                'message' => 'Logout berhasil',
            ]);
        } catch (Throwable $e) {
            Log::error('AuthController logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan logout.',
            ], 500);
        }
    }
}