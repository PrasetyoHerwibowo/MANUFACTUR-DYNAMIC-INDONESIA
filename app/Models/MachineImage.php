<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto galeri milik sebuah model mesin.
 */
class MachineImage extends Model
{
    protected $fillable = [
        'machine_id',
        'path',
        'caption',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function url(): string
    {
        return \App\Support\ImageUploader::url($this->path);
    }
}
