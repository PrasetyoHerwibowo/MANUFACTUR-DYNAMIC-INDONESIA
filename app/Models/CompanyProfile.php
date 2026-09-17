<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Profil perusahaan (satu baris data).
 */
class CompanyProfile extends Model
{
    protected $fillable = [
        'name',
        'tagline',
        'about',
        'vision',
        'mission',
        'address',
        'city',
        'phone',
        'whatsapp',
        'email',
        'website',
        'founded_year',
        'employees',
        'export_countries',
        'logo',
        'hero_image',
        'about_image',
        'map_embed',
        'facebook',
        'instagram',
        'linkedin',
        'youtube',
    ];

    /**
     * Nilai default agar tampilan website tetap rapi walau data belum diisi admin.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'name' => 'PT Manufactur Dynamic Indonesia',
        'tagline' => 'Spesialis Perancangan & Produksi Mesin Pengolahan Kopi dan Kakao',
        'email' => 'info@manufacturdynamic.co.id',
        'city' => 'Malang, Jawa Timur',
    ];

    public function logoUrl(): ?string
    {
        return $this->logo ? \App\Support\ImageUploader::url($this->logo) : null;
    }

    public function heroUrl(): string
    {
        return $this->hero_image
            ? \App\Support\ImageUploader::url($this->hero_image)
            : \App\Support\ImageUploader::placeholder();
    }

    public function aboutUrl(): string
    {
        return $this->about_image
            ? \App\Support\ImageUploader::url($this->about_image)
            : \App\Support\ImageUploader::placeholder();
    }

    /**
     * Visi/misi disimpan sebagai teks multi-baris.
     *
     * @return list<string>
     */
    public function missionList(): array
    {
        $items = preg_split('/\r\n|\r|\n/', (string) $this->mission) ?: [];

        return array_values(array_filter(array_map('trim', $items), fn ($item) => $item !== ''));
    }

    /**
     * Data yang sudah diisi admin saja (untuk ditampilkan di halaman publik).
     *
     * @return array<string, string>
     */
    public function contactDetails(): array
    {
        return array_filter([
            'Alamat' => $this->address,
            'Kota' => $this->city,
            'Telepon' => $this->phone,
            'WhatsApp' => $this->whatsapp,
            'Email' => $this->email,
            'Website' => $this->website,
        ], fn ($value) => filled($value));
    }
}
