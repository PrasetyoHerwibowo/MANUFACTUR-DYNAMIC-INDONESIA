<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model mesin beserta fungsi mesin dan spesifikasinya.
 */
class Machine extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'model_code',
        'function',
        'short_description',
        'description',
        'specifications',
        'capacity',
        'power',
        'dimension',
        'weight',
        'material',
        'main_image',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Nilai default form saat menambah data baru.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'sort_order' => 0,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(MachineImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function mainImageUrl(): ?string
    {
        if ($this->main_image) {
            return \App\Support\ImageUploader::url($this->main_image);
        }

        $first = $this->images->first();

        return $first ? $first->url() : null;
    }

    /**
     * Foto utama mesin, dipakai di tampilan publik.
     */
    public function getPhotoAttribute(): string
    {
        return $this->mainImageUrl() ?? \App\Support\ImageUploader::placeholder();
    }

    /**
     * Angka jumlah kapasitas (misal: 500 dari "500 kg/jam").
     */
    public function getCapacityAmountAttribute(): ?string
    {
        if (! $this->capacity) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*kg(?:\/(\d+)?\s*jam)?/i', $this->capacity, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^([\d\.,]+)/', $this->capacity, $matches)) {
            return $matches[1];
        }

        return $this->capacity;
    }

    /**
     * Angka durasi waktu kapasitas dalam jam (misal: 1 dari "500 kg/jam" atau 2 dari "500 kg/2 jam").
     */
    public function getCapacityTimeAttribute(): string
    {
        if (! $this->capacity) {
            return '1';
        }

        if (preg_match('/kg\/\s*(\d+)\s*jam/i', $this->capacity, $matches)) {
            return $matches[1];
        }

        return '1';
    }

    /**
     * Angka daya dalam kW (misal: "5.5" dari "5,5 kW / 3 phase").
     */
    public function getPowerKwAttribute(): ?string
    {
        if (! $this->power) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*kW/i', $this->power, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        if (preg_match('/^([\d\.,]+)/', $this->power, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        return null;
    }

    /**
     * Angka phase listrik (misal: "3" dari "5,5 kW / 3 phase").
     */
    public function getPowerPhaseAttribute(): ?string
    {
        if (! $this->power) {
            return null;
        }

        if (preg_match('/(\d+)\s*phase/i', $this->power, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Angka panjang dimensi dalam cm (misal: "180" dari "180 x 90 x 140 cm").
     */
    public function getDimensionLengthAttribute(): ?string
    {
        if (! $this->dimension) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*x\s*([\d\.,]+)\s*x\s*([\d\.,]+)/i', $this->dimension, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        return null;
    }

    /**
     * Angka lebar dimensi dalam cm (misal: "90" dari "180 x 90 x 140 cm").
     */
    public function getDimensionWidthAttribute(): ?string
    {
        if (! $this->dimension) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*x\s*([\d\.,]+)\s*x\s*([\d\.,]+)/i', $this->dimension, $matches)) {
            return str_replace(',', '.', $matches[2]);
        }

        return null;
    }

    /**
     * Angka tinggi dimensi dalam cm (misal: "140" dari "180 x 90 x 140 cm").
     */
    public function getDimensionHeightAttribute(): ?string
    {
        if (! $this->dimension) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*x\s*([\d\.,]+)\s*x\s*([\d\.,]+)/i', $this->dimension, $matches)) {
            return str_replace(',', '.', $matches[3]);
        }

        return null;
    }

    /**
     * Angka berat dalam kg (misal: "320" dari "320 kg").
     */
    public function getWeightAmountAttribute(): ?string
    {
        if (! $this->weight) {
            return null;
        }

        if (preg_match('/^([\d\.,]+)\s*kg/i', $this->weight, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        if (preg_match('/^([\d\.,]+)/', $this->weight, $matches)) {
            return str_replace(',', '.', $matches[1]);
        }

        return $this->weight;
    }

    /**
     * Spesifikasi tambahan dalam bentuk baris ["Label" => "Nilai"].
     *
     * @return array<string, string>
     */
    public function specificationLines(): array
    {
        $lines = [];
        $structured = [
            'Kapasitas' => $this->capacity,
            'Daya' => $this->power,
            'Dimensi' => $this->dimension,
            'Berat' => $this->weight,
            'Material' => $this->material,
        ];

        foreach ($structured as $label => $value) {
            if (filled($value)) {
                $lines[$label] = $value;
            }
        }

        foreach (preg_split('/\r\n|\r|\n/', (string) $this->specifications) as $line) {
            if (! filled(trim($line))) {
                continue;
            }

            $parts = explode(':', $line, 2);
            $label = trim($parts[0]);
            $value = isset($parts[1]) ? trim($parts[1]) : '';

            if ($value === '') {
                $lines[$label] = '-';
            } else {
                $lines[$label] = $value;
            }
        }

        return $lines;
    }
}
