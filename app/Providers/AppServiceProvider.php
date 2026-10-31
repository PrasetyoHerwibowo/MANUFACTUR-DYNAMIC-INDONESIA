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

        // Data jenis mesin dan model mesin untuk menu dropdown produk (MD-Cocoa & MD-Coffee Series)
        View::composer('public.partials.navbar', function ($view): void {
            $data = \Illuminate\Support\Facades\Cache::remember('site_nav_products', 1800, function () {
                $cocoaMachines = \App\Models\Machine::query()
                    ->active()
                    ->forCocoa()
                    ->ordered()
                    ->with('category')
                    ->get();

                $coffeeMachines = \App\Models\Machine::query()
                    ->active()
                    ->forCoffee()
                    ->ordered()
                    ->with('category')
                    ->get();

                $cocoaCategories = \App\Models\Category::query()
                    ->active()
                    ->forCocoa()
                    ->ordered()
                    ->with(['machines' => fn ($q) => $q->active()->forCocoa()->ordered()])
                    ->get();

                $coffeeCategories = \App\Models\Category::query()
                    ->active()
                    ->forCoffee()
                    ->ordered()
                    ->with(['machines' => fn ($q) => $q->active()->forCoffee()->ordered()])
                    ->get();

                return [
                    'navCocoaMachines' => $cocoaMachines,
                    'navCoffeeMachines' => $coffeeMachines,
                    'navCocoaCategories' => $cocoaCategories,
                    'navCoffeeCategories' => $coffeeCategories,
                ];
            });

            $view->with($data);
        });
    }
}
