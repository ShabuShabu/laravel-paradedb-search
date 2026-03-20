<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\Commands\Help;
use ShabuShabu\ParadeDB\Commands\IndexIntegrity;
use ShabuShabu\ParadeDB\Commands\TestTable;
use ShabuShabu\ParadeDB\Commands\Tokenizers;
use ShabuShabu\ParadeDB\Commands\VersionInfo;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\v1\Parse;
use ShabuShabu\ParadeDB\Expressions\v1\Score;
use ShabuShabu\ParadeDB\Expressions\v1\Snippet;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\TokenizerExpression;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Operators\FullText;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tpetry\PostgresqlEnhanced\Query\Grammar;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;

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
                VersionInfo::class,
                IndexIntegrity::class,
            )
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('ShabuShabu/laravel-paradedb-search');
            });
    }

    public function bootingPackage(): void
    {
        Grammar::customOperators(
            collect(Distance::cases())
                ->map(fn (Distance $distance) => $distance->value)
                ->merge(
                    collect(FullText::cases())->map(
                        fn (FullText $operator) => $operator->value
                    )
                )
                ->all()
        );
    }

    public function registeringPackage(): void
    {
        // Note: v1 only
        Builder::macro('selectWithScore', function (array $columns = ['*'], string $key = 'id') {
            return $this->select([...$columns, new Score($key)]);
        });

        // Note: v1 only
        Builder::macro('selectWithSnippet', function (string $field, array $columns = ['*'], ?string $startTag = null, ?string $endTag = null, ?int $maxNumChars = null) {
            return $this->select([...$columns, new Snippet($field, $startTag, $endTag, $maxNumChars)]);
        });

        // Note: v1 only
        Builder::macro('whereSearch', function (ParadeExpression | Query | string $expression, string $field = 'id') {
            if ($expression instanceof Query) {
                $expression = new Parse($expression);
            }

            return $this->where($field, FullText::search->value, $expression);
        });

        // Note: v2 only
        Blueprint::macro('bm25', function (array $columns, ?array $parameters = null, ?string $name = null): Fluent {
            $table = $this->table; // @phpstan-ignore-line
            $grammar = $this->grammar; // @phpstan-ignore-line

            $name ??= sprintf('%s_bm25_%s', $table, config('paradedb-search.index_suffix'));
            $columns = array_map(
                static fn (string | TokenizerExpression $column) => $column instanceof TokenizerExpression
                    ? Str::wrap($column->getValue($grammar), '(', ')')
                    : $column,
                $columns,
            );

            return $this
                ->index($columns, $name)
                ->algorithm('bm25')
                ->with($parameters ?? ['key_field' => 'id']);
        });
    }
}
