<?php

namespace Agency\ProductSample\Providers;

use Illuminate\Support\ServiceProvider;

class ProductSampleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'product-sample');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'product-sample');

        $this->mergeConfigFrom(__DIR__ . '/../Config/admin-menu.php', 'menu.admin');
        $this->mergeConfigFrom(__DIR__ . '/../Config/acl.php', 'acl');
        $this->mergeConfigFrom(__DIR__ . '/../Config/system.php', 'core');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/product-sample.php', 'product-sample');
    }
}
