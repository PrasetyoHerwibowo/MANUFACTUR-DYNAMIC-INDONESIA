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
        // Profil perusahaan tersedia di seluruh view utama & layout ({{ $company->name }}).
        View::composer(['public.*', 'admin.*', 'layouts.*', 'mail.*', 'errors.*'], function ($view): void {
            $view->with('company', Site::company());
        });
    }
}
