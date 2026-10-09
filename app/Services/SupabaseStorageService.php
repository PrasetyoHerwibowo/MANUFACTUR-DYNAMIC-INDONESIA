<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    private string $url;
    private string $key;
    private string $bucket;

    /**
     * Tipe MIME gambar yang diizinkan dan ekstensi amannya.
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
    ];

    public function __construct()
    {
        $this->url = rtrim((string) config('services.supabase.url', env('SUPABASE_URL', '')), '/');
        $this->key = (string) config('services.supabase.key', env('SUPABASE_KEY', ''));
        $this->bucket = (string) config('services.supabase.bucket', env('SUPABASE_STORAGE_BUCKET', 'uploads'));
    }

    /**
     * Cek apakah konfigurasi Supabase aktif dan lengkap.
     */
    public function isConfigured(): bool
    {
        return filled($this->url) && filled($this->key) && filled($this->bucket);
    }

    /**
     * Upload berkas gambar ke Supabase Storage dan kembalikan Public URL.
     *
     * @param UploadedFile|null $file Berkas upload
     * @param string $folder Direktori di dalam bucket (misal: 'machines', 'profile', 'avatars')
     * @return string|null Public URL gambar atau null jika gagal/file kosong
     */
    public function upload(?UploadedFile $file, string $folder = 'uploads'): ?string
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        // Sanitasi MIME Type dan ekstensi
        $mime = $file->getMimeType();
        if (! isset(self::ALLOWED_MIME_TYPES[$mime])) {
            Log::warning('SupabaseStorageService: Tipe berkas tidak diizinkan', [
                'mime' => $mime,
                'original' => $file->getClientOriginalName(),
            ]);
            return null;
        }

        $extension = self::ALLOWED_MIME_TYPES[$mime];

        // Buat nama file yang aman & acak untuk mencegah penimpaan / path traversal
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = Str::limit($safeName ?: 'img', 40, '');
        $filename = $safeName . '-' . Str::lower(Str::random(12)) . '.' . $extension;

        $cleanFolder = trim(preg_replace('/[^a-zA-Z0-9_\-\/]/', '', $folder), '/');
        $path = $cleanFolder !== '' ? "{$cleanFolder}/{$filename}" : $filename;

        if (! $this->isConfigured()) {
            Log::warning('SupabaseStorageService: Konfigurasi Supabase belum lengkap, upload dibatalkan.');
            return null;
        }

        try {
            $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";
            $fileContent = file_get_contents($file->getRealPath());

            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$this->key}",
                'Content-Type' => $mime,
            ])
            ->timeout(20)
            ->withBody($fileContent, $mime)
            ->post($endpoint);

            if (! $response->successful()) {
                Log::error('SupabaseStorageService: Gagal upload ke Supabase', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'path' => $path,
                ]);
                return null;
            }

            return $this->getPublicUrl($path);
        } catch (Exception $e) {
            Log::error('SupabaseStorageService Exception: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
            ]);
            return null;
        }
    }

    /**
     * Hapus berkas gambar dari Supabase Storage jika merupakan URL Supabase.
     */
    public function delete(?string $urlOrPath): bool
    {
        if (! filled($urlOrPath) || ! $this->isConfigured()) {
            return false;
        }

        $path = $this->extractPath($urlOrPath);
        if (! $path) {
            return false;
        }

        try {
            $endpoint = "{$this->url}/storage/v1/object/{$this->bucket}";

            $response = Http::withHeaders([
                'apikey' => $this->key,
                'Authorization' => "Bearer {$this->key}",
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->delete($endpoint, [
                'prefixes' => [$path],
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('SupabaseStorageService Delete Exception: ' . $e->getMessage(), [
                'path' => $path,
            ]);
            return false;
        }
    }

    /**
     * Dapatkan Public URL untuk path di bucket publik Supabase.
     */
    public function getPublicUrl(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$cleanPath}";
    }

    /**
     * Ekstrak path objek di dalam bucket dari Public URL Supabase.
     */
    private function extractPath(string $urlOrPath): ?string
    {
        $prefix = "{$this->url}/storage/v1/object/public/{$this->bucket}/";
        if (Str::startsWith($urlOrPath, $prefix)) {
            return Str::after($urlOrPath, $prefix);
        }

        if (! Str::startsWith($urlOrPath, ['http://', 'https://'])) {
            return ltrim($urlOrPath, '/');
        }

        return null;
    }
}
