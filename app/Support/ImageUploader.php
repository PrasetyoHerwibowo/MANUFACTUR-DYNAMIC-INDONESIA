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
     * Ekstensi yang diizinkan beserta tipe MIME yang sesuai.
     *
     * Nama berkas disimpan memakai ekstensi dari daftar ini (bukan ekstensi
     * kiriman peramban) supaya berkas gambar tidak dapat disimpan dengan
     * ekstensi berbahaya seperti .php.
     *
     * @var array<string, string>
     */
    private const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

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

        $filename = $name.'-'.Str::lower(Str::random(8)).'.'.self::extension($file);
        $directory = trim($folder, '/');

        Storage::disk(self::DISK)->putFileAs($directory, $file, $filename);

        return $directory.'/'.$filename;
    }

    /**
     * Ekstensi aman untuk berkas yang diunggah.
     *
     * Diprioritaskan dari tipe MIME asli berkas; bila tipe tidak dikenali,
     * ekstensi kiriman peramban dibersihkan lebih dahulu (hanya huruf/angka).
     */
    private static function extension(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if ($mime && isset(self::EXTENSIONS[$mime])) {
            return self::EXTENSIONS[$mime];
        }

        $extension = Str::lower(preg_replace('/[^A-Za-z0-9]/', '', $file->getClientOriginalExtension()) ?? '');

        return $extension !== '' ? Str::limit($extension, 5, '') : 'jpg';
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
