<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Role user: admin atau customer/client
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'avatar',
        'password',
        'is_active',
        'activation_code',
        'activation_code_expires_at',
        'auth_provider',
        'google_id',
        'reset_password_code',
        'reset_password_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_code',
        'reset_password_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'activation_code_expires_at' => 'datetime',
            'reset_password_expires_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user memiliki status aktif dan terverifikasi.
     */
    public function isActivated(): bool
    {
        return (bool) $this->is_active && $this->email_verified_at !== null;
    }

    /**
     * Cek apakah user memiliki role admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * URL foto profil admin (fallback ke avatar inisial nama).
     */
    public function avatarUrl(): string
    {
        return $this->avatar
            ? \App\Support\ImageUploader::url($this->avatar)
            : 'https://ui-avatars.com/api/?background=8a5a2b&color=fff&name='.urlencode((string) $this->name);
    }
}
