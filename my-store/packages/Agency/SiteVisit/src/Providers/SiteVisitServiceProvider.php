<?php

namespace Agency\SiteVisit\Providers;

use Illuminate\Support\ServiceProvider;

class SiteVisitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'site-visit');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'site-visit');

        $this->mergeConfigFrom(__DIR__ . '/../Config/admin-menu.php', 'menu.admin');
        $this->mergeConfigFrom(__DIR__ . '/../Config/acl.php', 'acl');
        $this->mergeConfigFrom(__DIR__ . '/../Config/system.php', 'core');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/site-visit.php', 'site-visit');
    }
}
