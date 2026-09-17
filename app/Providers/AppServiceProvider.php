<?php

namespace App\Providers;

use App\Support\Site;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Profil perusahaan tersedia di seluruh view ({{ $company->name }}).
        View::composer('*', function ($view): void {
            $view->with('company', Site::company());
        });
    }
}
