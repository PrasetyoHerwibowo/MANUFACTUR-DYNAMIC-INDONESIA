<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Pesan yang dikirim pengunjung melalui formulir kontak.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Seluruh balasan admin terhadap pesan ini (terlama ke terbaru).
     *
     * @return HasMany<ContactMessageReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ContactMessageReply::class);
    }

    /**
     * Balasan terakhir yang benar-benar terkirim.
     *
     * @return HasOne<ContactMessageReply, $this>
     */
    public function lastSentReply(): HasOne
    {
        return $this->hasOne(ContactMessageReply::class)
            ->ofMany(
                fn (Builder $query) => $query->where('status', ContactMessageReply::STATUS_SENT),
                'id',
                'max'
            );
    }
}
