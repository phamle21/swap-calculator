<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Repositories\SwapCalculationRepository::class);
        $this->app->bind(\App\Repositories\SwapRateRepository::class);
        $this->app->bind(\App\Services\SwapService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure application locale is set from session on every request so
        // translations loaded in Blade reflect the user's selected language.
        $locale = session('locale', config('app.locale'));
        App::setLocale($locale);
    }
}
