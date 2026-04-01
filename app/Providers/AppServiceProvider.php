<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        \App\Models\Marca::observe(\App\Observers\MarcaObserver::class);
        \App\Models\Auto::observe(\App\Observers\AutoObserver::class);
        Paginator::useBootstrapFive();
    }
}
