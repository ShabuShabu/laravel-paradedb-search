<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ParadeDBServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-paradedb-search')
            ->hasConfigFile();
    }
}
