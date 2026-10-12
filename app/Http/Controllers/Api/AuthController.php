<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'phone' => $validated['phone'] ?? '-',
            'company' => $validated['company'] ?? 'Perusahaan Kopi / Mitra',
            'address' => '-',
        ]);

        $token = $user->createToken('manufaktur-mobile')->plainTextToken;

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

        $email = strtolower(trim($validated['email']));
        $providerName = ucfirst($validated['provider']);

        // Check if user already exists
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
                'phone' => $validated['phone'] ?? '-',
                'company' => $companyName,
                'address' => '-',
            ]);
        }

        $token = $user->createToken('manufaktur-mobile-sso')->plainTextToken;

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
        $user = $request->user();
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
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}