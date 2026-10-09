<?php

namespace App\Support;

use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Helper unggah/ hapus foto.
 *
 * Mendukung penyimpanan ke Supabase Storage (Object Storage) secara otomatis
 * jika terkonfigurasi, dengan fallback ke disk "uploads" lokal (folder public/uploads).
 */
class ImageUploader
{
    public const DISK = 'uploads';

    /**
     * Ekstensi yang diizinkan beserta tipe MIME yang sesuai.
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
     * Simpan file foto ke Supabase Storage (atau lokal jika belum terkonfigurasi).
     * Mengembalikan URL string publik (Supabase) atau path relatif (Lokal).
     */
    public static function store(?UploadedFile $file, string $folder): ?string
    {
        if (! $file) {
            return null;
        }

        $supabase = app(SupabaseStorageService::class);

        if ($supabase->isConfigured()) {
            $supabaseUrl = $supabase->upload($file, $folder);
            if ($supabaseUrl) {
                return $supabaseUrl;
            }
        }

        // Fallback ke penyimpanan lokal disk uploads bila Supabase gagal/tidak aktif
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $name = Str::limit($name ?: 'foto', 40, '');

        $filename = $name.'-'.Str::lower(Str::random(8)).'.'.self::extension($file);
        $directory = trim($folder, '/');

        Storage::disk(self::DISK)->putFileAs($directory, $file, $filename);

        return $directory.'/'.$filename;
    }

    /**
     * Ekstensi aman untuk berkas yang diunggah.
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
     * Hapus foto dari Supabase Storage atau disk lokal.
     */
    public static function delete(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $supabase = app(SupabaseStorageService::class);
            $supabase->delete($path);
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
