<?php

namespace Sagautam5\EmailBlocker\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Sagautam5\EmailBlocker\Providers\EmailBlockServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

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
}
