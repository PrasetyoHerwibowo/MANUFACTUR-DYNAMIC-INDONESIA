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

        if ($request->hasFile('avatar')) {
            ImageUploader::delete($user->avatar);
            $validated['avatar'] = ImageUploader::store($request->file('avatar'), 'avatars');
        }

        $user->update($validated);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    /**
     * Ganti password admin.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        $user->update(['password' => $validated['password']]);

        return redirect()
            ->route('admin.account.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
