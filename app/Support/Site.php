<?php

namespace App\Support;

use App\Models\CompanyProfile;
use Throwable;

/**
 * Data global website (profil perusahaan) yang dibagikan ke seluruh view.
 */
class Site
{
    protected static ?CompanyProfile $company = null;

    /**
     * Profil perusahaan aktif (hanya satu baris data).
     */
    public static function company(): CompanyProfile
    {
        if (static::$company === null) {
            try {
                static::$company = CompanyProfile::query()->first() ?? new CompanyProfile();
            } catch (Throwable) {
                static::$company = new CompanyProfile();
            }
        }

        return static::$company;
    }

    /**
     * Bersihkan cache in-memory setelah admin memperbarui profil.
     */
    public static function flush(): void
    {
        static::$company = null;
    }
}
