<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ShabuShabu\ParadeDB\ParadeDBServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ParadeDBServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
