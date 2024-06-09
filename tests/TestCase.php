<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use ShabuShabu\ParadeDB\ParadeDBServiceProvider;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            ParadeDBServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/app/database/migrations');
    }
}
