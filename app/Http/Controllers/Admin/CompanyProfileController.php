<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Support\ImageUploader;
use App\Support\Site;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    /**
     * Form edit profil perusahaan.
     */
    public function edit(): View
    {
        $profile = Site::company();

        return view('admin.profile.edit', compact('profile'));
    }

    /**
     * Perbarui profil perusahaan (termasuk logo & foto).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'tagline' => ['nullable', 'string', 'max:250'],
            'about' => ['nullable', 'string', 'max:20000'],
            'vision' => ['nullable', 'string', 'max:5000'],
            'mission' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'website' => ['nullable', 'string', 'max:150'],
            'founded_year' => ['nullable', 'string', 'max:10'],
            'employees' => ['nullable', 'string', 'max:60'],
            'export_countries' => ['nullable', 'string', 'max:150'],
            'map_embed' => ['nullable', 'string', 'max:5000'],
            'facebook' => ['nullable', 'string', 'max:200'],
            'instagram' => ['nullable', 'string', 'max:200'],
            'linkedin' => ['nullable', 'string', 'max:200'],
            'youtube' => ['nullable', 'string', 'max:200'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:4096'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
            'about_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144'],
        ]);

        $profile = CompanyProfile::query()->first() ?? new CompanyProfile();

        foreach (['logo' => 'profile', 'hero_image' => 'profile', 'about_image' => 'profile'] as $field => $folder) {
            if ($request->hasFile($field)) {
                ImageUploader::delete($profile->{$field});
                $validated[$field] = ImageUploader::store($request->file($field), $folder);
            }
        }

        $profile->fill($validated)->save();
        Site::flush();

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}
