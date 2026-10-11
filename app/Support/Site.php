<?php

namespace App\Support;

use App\Models\CompanyProfile;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Data global website (profil perusahaan) yang dibagikan ke seluruh view.
 */
class Site
{
    protected static ?CompanyProfile $company = null;

    /**
     * Profil perusahaan aktif (hanya satu baris data, dicache 24 jam).
     */
    public static function company(): CompanyProfile
    {
        if (static::$company === null) {
            try {
                static::$company = Cache::remember('site_company_profile', 86400, function (): CompanyProfile {
                    return CompanyProfile::query()->first() ?? new CompanyProfile();
                });
            } catch (Throwable) {
                static::$company = new CompanyProfile();
            }
        }

        return static::$company;
    }

    /**
     * Bersihkan cache in-memory dan file cache setelah admin memperbarui profil.
     */
    public static function flush(): void
    {
        static::$company = null;
        Cache::forget('site_company_profile');
        static::flushPublicCache();
    }

    /**
     * Bersihkan cache publik untuk katalog & kategori ketika ada perubahan data.
     */
    public static function flushPublicCache(): void
    {
        Cache::forget('home_categories');
        Cache::forget('home_featured');
        Cache::forget('home_stats');
        Cache::forget('products_categories');
        static::flushAdminDashboardCache();
    }

    /**
     * Bersihkan cache dashboard admin ketika ada perubahan data model mesin atau kategori.
     */
    public static function flushAdminDashboardCache(): void
    {
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_dashboard_recent_machines');
        Cache::forget('admin_dashboard_category_chart');
    }
}
