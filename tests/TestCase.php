<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use ShabuShabu\ParadeDB\ParadeDBServiceProvider;
use Tpetry\PostgresqlEnhanced\PostgresqlEnhancedServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
            PostgresqlEnhancedServiceProvider::class,
            ParadeDBServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/app/database/migrations');
    }
}
