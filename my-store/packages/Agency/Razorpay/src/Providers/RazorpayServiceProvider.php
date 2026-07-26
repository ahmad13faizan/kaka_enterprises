<?php

namespace Agency\Razorpay\Providers;

use Illuminate\Support\ServiceProvider;

class RazorpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'razorpay');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'razorpay');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the Razorpay payment method into Bagisto's payment methods list.
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/paymentmethods.php',
            'payment_methods'
        );

        // Register the admin configuration fields (key id/secret, status, etc.).
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/system.php',
            'core'
        );
    }
}
