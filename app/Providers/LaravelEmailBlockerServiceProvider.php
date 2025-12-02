<?php

namespace Sagautam5\LaravelEmailBlocker\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelEmailBlockerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(LaravelEmailEventServiceProvider::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../../config/mail-blocker.php', 'mail-blocker');

        $this->publishes([
            __DIR__.'/../../config/mail-blocker.php' => config_path('mail-blocker.php'),
        ]);

        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ]);
    }
}