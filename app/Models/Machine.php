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
