<?php

namespace Sagautam5\EmailBlocker\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Sagautam5\EmailBlocker\Providers\EmailBlockServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Sagautam5\\EmailBlocker\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            EmailBlockServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            __DIR__.'/../database/migrations'
        );
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('email-blocker', [
            'block_enabled' => true,
            'log_enabled' => false,
            'rules' => [],
            'settings' => [
                'global_block' => false,
                'time_window' => [
                    'from' => null,
                    'to' => null,
                    'timezone' => null,
                ],
                'blocked_environments' => [],
                'blocked_domains' => [],
                'blocked_mailables' => [],
                'blocked_emails' => [],
            ],
        ]);
    }
}
