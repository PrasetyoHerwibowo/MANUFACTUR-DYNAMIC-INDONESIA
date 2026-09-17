<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Helper unggah/ hapus foto.
 *
 * Foto disimpan pada disk "uploads" (folder public/uploads) agar file
 * langsung dapat diakses pada instalasi XAMPP tanpa perlu symlink storage.
 */
class ImageUploader
{
    public const DISK = 'uploads';

    /**
     * Simpan file foto dan kembalikan path relatifnya (mis. machines/xxx.jpg).
     */
    public static function store(?UploadedFile $file, string $folder): ?string
    {
        if (! $file) {
            return null;
        }

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $name = Str::limit($name ?: 'foto', 40, '');

        $filename = $name.'-'.Str::lower(Str::random(8)).'.'.$file->getClientOriginalExtension();
        $directory = trim($folder, '/');

        Storage::disk(self::DISK)->putFileAs($directory, $file, $filename);

        return $directory.'/'.$filename;
    }

    /**
     * Hapus foto dari disk.
     */
    public static function delete(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        $disk = Storage::disk(self::DISK);

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    /**
     * URL publik sebuah foto.
     */
    public static function url(?string $path): string
    {
        if (! filled($path)) {
            return self::placeholder();
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset('uploads/'.ltrim($path, '/'));
    }

    /**
     * URL gambar placeholder bila foto belum diunggah admin.
     */
    public static function placeholder(): string
    {
        return asset('uploads/placeholder.svg');
    }
}
