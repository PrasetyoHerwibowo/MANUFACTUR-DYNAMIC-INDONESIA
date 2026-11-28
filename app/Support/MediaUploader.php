<?php

namespace App\Support;

use App\Services\SupabaseStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class MediaUploader
{
    public const MAX_BYTES = 26214400; // 25 MB

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'mp4'];

    public const VIDEO_EXTENSIONS = ['mp4'];

    /**
     * Simpan file foto/video bukti klaim garansi.
     * Menerima UploadedFile atau Base64 string dengan batasan maksimal 25 MB.
     *
     * @return array{path: string, file_type: string, filename: string}|null
     */
    public static function store(?UploadedFile $file, string $folder = 'warranty'): ?array
    {
        if (! $file || ! $file->isValid()) {
            return null;
        }

        // Cek ukuran berkas (maks 25 MB)
        if ($file->getSize() > self::MAX_BYTES) {
            throw new \InvalidArgumentException('Ukuran file melebihi batas maksimal 25 MB.');
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \InvalidArgumentException('Format file tidak didukung. Harap unggah berkas JPG, JPEG, PNG, atau MP4.');
        }

        // Verifikasi Magic Bytes & Tolak SVG
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 512);
        fclose($handle);

        if (stripos($header, '<svg') !== false || stripos($header, '<?xml') !== false) {
            throw new \InvalidArgumentException('Format SVG tidak diizinkan! Harap unggah foto (JPG/PNG) atau video (MP4).');
        }

        $isJpeg = strncmp($header, "\xFF\xD8\xFF", 3) === 0;
        $isPng = strncmp($header, "\x89PNG\r\n\x1a\n", 8) === 0;
        $isMp4 = strlen($header) >= 8 && substr($header, 4, 4) === 'ftyp';

        if (in_array($extension, ['jpg', 'jpeg'], true) && ! $isJpeg) {
            throw new \InvalidArgumentException('Berkas foto JPG tidak valid atau telah dimanipulasi.');
        }
        if ($extension === 'png' && ! $isPng) {
            throw new \InvalidArgumentException('Berkas foto PNG tidak valid atau telah dimanipulasi.');
        }
        if ($extension === 'mp4' && ! $isMp4) {
            throw new \InvalidArgumentException('Berkas video MP4 tidak valid atau telah dimanipulasi.');
        }
        if (! $isJpeg && ! $isPng && ! $isMp4) {
            throw new \InvalidArgumentException('Konten berkas tidak valid.');
        }

        $isMp4Final = $isMp4 || in_array($extension, self::VIDEO_EXTENSIONS, true);
        $fileType = $isMp4Final ? 'video' : 'image';

        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = Str::limit($safeName ?: ($isMp4 ? 'video_kerusakan' : 'bukti_kerusakan'), 40, '');
        $filename = $safeName . '_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $extension;

        // 1. Simpan salinan lokal di public/uploads/{folder} agar selalu dapat diakses seketika
        $localDir = public_path('uploads/' . trim($folder, '/'));
        if (! file_exists($localDir)) {
            @mkdir($localDir, 0755, true);
        }

        try {
            $file->move($localDir, $filename);
            $localUrl = asset('uploads/' . trim($folder, '/') . '/' . $filename);
        } catch (Throwable $e) {
            Log::error('MediaUploader move error: ' . $e->getMessage());
            $localUrl = null;
        }

        $finalPath = $localUrl ?: ('uploads/' . trim($folder, '/') . '/' . $filename);

        return [
            'path' => $finalPath,
            'file_type' => $fileType,
            'filename' => $filename,
        ];
    }
}

