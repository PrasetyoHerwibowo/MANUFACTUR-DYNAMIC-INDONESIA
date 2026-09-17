<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto halaman katalog.
 */
class CatalogImage extends Model
{
    protected $fillable = [
        'catalog_id',
        'path',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function url(): string
    {
        return \App\Support\ImageUploader::url($this->path);
    }
}
