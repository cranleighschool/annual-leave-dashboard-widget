<?php

declare(strict_types=1);

namespace CranleighSchool\AnnualLeave;

use Illuminate\Support\ServiceProvider;

class AnnualLeaveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/annual_leave.php',
            'annual_leave'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/annual_leave.php' => config_path('annual_leave.php'),
        ], 'annual-leave-config');

        // Load routes if enabled
        if (config('annual_leave.enable_routes', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'annual-leave');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/annual-leave'),
        ], 'annual-leave-views');
    }
}
