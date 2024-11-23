<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use ShabuShabu\ParadeDB\Commands\Help;
use ShabuShabu\ParadeDB\Commands\TestTable;
use ShabuShabu\ParadeDB\Commands\Tokenizers;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\Snippet;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Operators\FullText;
use ShabuShabu\ParadeDB\TantivyQL\Query;
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
                Tokenizers::class,
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('ShabuShabu/laravel-paradedb-search');
            });
    }

    public function bootingPackage(): void
    {
        $operators = collect(Distance::cases())
            ->map(fn (Distance $distance) => $distance->value)
            ->prepend(FullText::search->value)
            ->all();

        PostgresGrammar::customOperators($operators);
    }

    public function registeringPackage(): void
    {
        Builder::macro('selectWithScore', function (array $columns = ['*'], string $key = 'id') {
            return $this->select([...$columns, new Score($key)]);
        });

        Builder::macro('selectWithSnippet', function (string $field, array $columns = ['*'], ?string $startTag = null, ?string $endTag = null, ?int $maxNumChars = null) {
            return $this->select([...$columns, new Snippet($field, $startTag, $endTag, $maxNumChars)]);
        });

        Builder::macro('whereSearch', function (ParadeExpression | Query | string $expression, string $field = 'id') {
            if ($expression instanceof Query) {
                $expression = new Parse($expression);
            }

            return $this->where($field, FullText::search->value, $expression);
        });
    }
}
