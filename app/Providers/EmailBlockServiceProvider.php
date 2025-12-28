<?php

namespace Sagautam5\EmailBlocker\Providers;

use Illuminate\Support\ServiceProvider;
use Sagautam5\EmailBlocker\Console\Commands\ValidateEmailBlockerConfigCommand;
use Sagautam5\EmailBlocker\Services\EmailLogger;
use Sagautam5\EmailBlocker\Validators\EmailBlockerConfigValidator;

class EmailBlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(EmailEventServiceProvider::class);
        $this->app->singleton('email-blocker.logger', EmailLogger::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->mergeConfigFrom(__DIR__.'/../../config/email-blocker.php', 'email-blocker');

        $this->publishes([
            __DIR__.'/../../config/email-blocker.php' => config_path('email-blocker.php'),
        ], 'email-blocker-config');

        $this->publishesMigrations([
            __DIR__.'/../../database/migrations' => database_path('migrations'),
        ], 'email-blocker-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ValidateEmailBlockerConfigCommand::class,
            ]);
        }else{
            EmailBlockerConfigValidator::validate(
                config('email-blocker')
            );
        }
    }
}
