<?php

namespace Sagautam5\EmailBlocker\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Sagautam5\EmailBlocker\Providers\EmailBlockServiceProvider;

abstract class TestCase extends BaseTestCase
{
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
