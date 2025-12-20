<?php

namespace Sagautam5\EmailBlocker\Providers;

use Illuminate\Support\ServiceProvider;
use Sagautam5\EmailBlocker\Services\EmailLogger;

class EmailBlockServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(EmailEventServiceProvider::class);
        $this->app->singleton('email-blocker.logger', EmailLogger::class);
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../../config/email-blocker.php', 'email-blocker');

        $this->publishes([
            __DIR__.'/../../config/email-blocker.php' => config_path('email-blocker.php'),
        ]);

        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ]);
    }
}
