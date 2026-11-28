<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kategori mesin (jenis mesin).
 */
class Category extends Model
{
    protected $fillable = [
        'name',
        'series',
        'slug',
        'image',
        'tagline',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * URL foto jenis mesin (dari disk uploads / storage).
     */
    public function imageUrl(): ?string
    {
        if (! filled($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return \App\Support\ImageUploader::url($this->image);
    }

    /**
     * Nilai default form saat menambah data baru.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function machines(): HasMany
    {
        return $this->hasMany(Machine::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeForCoffee(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('series', 'coffee')
              ->orWhere('series', 'both')
              ->orWhere(function ($sub) {
                  $sub->whereNull('series')
                      ->where(function ($inner) {
                          $inner->where('name', 'like', '%kopi%')
                                ->orWhere('name', 'like', '%coffee%')
                                ->orWhere('slug', 'like', '%kopi%')
                                ->orWhere(function ($notCocoa) {
                                    $notCocoa->where('name', 'not like', '%kakao%')
                                             ->where('name', 'not like', '%cocoa%');
                                });
                      });
              });
        });
    }

    public function scopeForCocoa(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('series', 'cocoa')
              ->orWhere('series', 'both')
              ->orWhere(function ($sub) {
                  $sub->whereNull('series')
                      ->where(function ($inner) {
                          $inner->where('name', 'like', '%kakao%')
                                ->orWhere('name', 'like', '%cocoa%')
                                ->orWhere('name', 'like', '%cokelat%')
                                ->orWhere('slug', 'like', '%kakao%');
                      });
              });
        });
    }
}
