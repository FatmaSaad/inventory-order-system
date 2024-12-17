<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Ingredient;

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
        Ingredient::observe(\App\Observers\StockObserver::class);

    }
}
