<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Form pengaturan akun admin.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('admin.account.edit', compact('user'));
    }

    /**
     * Perbarui data akun admin (nama, email, telepon, avatar).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $uploadedAvatar = null;
        try {
            if ($request->hasFile('avatar')) {
                $uploadedAvatar = ImageUploader::store($request->file('avatar'), 'avatars');
                $validated['avatar'] = $uploadedAvatar;
            }

            $oldAvatar = $user->avatar;
            $user->update($validated);

            if ($uploadedAvatar && $oldAvatar && $oldAvatar !== $uploadedAvatar) {
                ImageUploader::delete($oldAvatar);
            }

            // Sinkronkan nomor telepon ke profil perusahaan jika diisi / diubah di pengaturan akun
            if (array_key_exists('phone', $validated)) {
                $company = \App\Models\CompanyProfile::first();
                if ($company) {
                    $company->update(['phone' => $validated['phone']]);
                    \App\Support\Site::flush();
                }
            }

            return redirect()
                ->route('admin.account.edit')
                ->with('success', 'Data akun berhasil diperbarui.');
        } catch (\Throwable $e) {
            if ($uploadedAvatar) {
                ImageUploader::delete($uploadedAvatar);
            }
            \Illuminate\Support\Facades\Log::error('AccountController@update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data akun: '.$e->getMessage());
        }
    }

    /**
     * Ganti password admin.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            $user = $request->user();
            $user->update(['password' => $validated['password']]);

            return redirect()
                ->route('admin.account.edit')
                ->with('success', 'Password berhasil diperbarui.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AccountController@updatePassword error: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal memperbarui password: '.$e->getMessage());
        }
    }
}
