<?php

namespace Agency\AdminGuard\Providers;

use Illuminate\Support\ServiceProvider;

class AdminGuardServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'admin-guard');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/admin-guard.php', 'admin-guard');
    }
}
