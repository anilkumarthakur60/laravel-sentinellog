<?php

declare(strict_types=1);

namespace YourName\SentinelLog;

use Illuminate\Support\ServiceProvider;

class SentinelLogServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sentinel-log.php',
            'sentinel-log'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Publish the config file
        $this->publishes([
            __DIR__ . '/../config/sentinel-log.php' => config_path('sentinel-log.php'),
        ], 'sentinel-log-config');

        // Prepare for migrations (to be added in Step 2)
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}