<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Commands\IndexIntegrity;
use ShabuShabu\ParadeDB\Commands\TestTable;
use ShabuShabu\ParadeDB\Commands\Tokenizers;
use ShabuShabu\ParadeDB\Commands\VersionInfo;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\v1\Parse;
use ShabuShabu\ParadeDB\Expressions\v1\Score;
use ShabuShabu\ParadeDB\Expressions\v1\Snippet;
use ShabuShabu\ParadeDB\Expressions\v2\Casts\Row;
use ShabuShabu\ParadeDB\Expressions\v2\Support\Type;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\TokenizerExpression;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Operators\FullText;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tpetry\PostgresqlEnhanced\Query\Grammar;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

class ParadeDBServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-paradedb-search')
            ->hasConfigFile()
            ->hasCommands(
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

        // Note: both v1 and v2
        Builder::macro('search', function () {
            return $this->withoutGlobalScopes(
                config('paradedb-search.remove_global_scopes')
            );
        });

        // Note: v2 only
        Schema::macro('createCompositeType', function (string $name, array $columns) {
            $grammar = $this->grammar; // @phpstan-ignore-line

            $wrapColumn = static function (string $column) use ($grammar) {
                $parts = explode(' ', $column, 2);

                if (count($parts) !== 2) {
                    throw new InvalidArgumentException("Invalid column format: $column");
                }

                return sprintf('%s %s', $grammar->wrap($parts[0]), $parts[1]);
            };

            $columns = array_map(
                static fn (string | TokenizerExpression | Type $column) => match (true) {
                    $column instanceof TokenizerExpression => $column->useAsType()->getValue($grammar),
                    $column instanceof Type => $column->getValue($grammar),
                    default => $wrapColumn($column),
                },
                $columns,
            );

            $statement = sprintf(
                'create type %s as (%s)',
                $grammar->wrap($name),
                implode(', ', $columns)
            );

            return $this->connection->statement($statement); // @phpstan-ignore-line
        });

        // Note: v2 only
        Blueprint::macro('bm25', function (array $columns, ?array $parameters = null, ?string $name = null): Fluent {
            $table = $this->table; // @phpstan-ignore-line
            $grammar = $this->grammar; // @phpstan-ignore-line

            $name ??= sprintf('%s_bm25_%s', $table, config('paradedb-search.index_suffix'));
            $columns = array_map(
                static fn (string | TokenizerExpression | Row $column) => $column instanceof TokenizerExpression || $column instanceof Row
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
