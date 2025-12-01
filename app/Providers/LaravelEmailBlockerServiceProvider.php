<?php

namespace Sagautam5\LaravelEmailBlocker\App\Providers;

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
    }
}