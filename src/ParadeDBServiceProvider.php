<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Database\Query\Grammars\PostgresGrammar;
use ShabuShabu\ParadeDB\Commands\Help;
use ShabuShabu\ParadeDB\Commands\TestTable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ParadeDBServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-paradedb-search')
            ->hasConfigFile()
            ->hasCommands(
                Help::class,
                TestTable::class,
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('ShabuShabu/laravel-paradedb-search');
            });
    }

    public function bootingPackage(): void
    {
        PostgresGrammar::customOperators(['@@@']);
    }
}
