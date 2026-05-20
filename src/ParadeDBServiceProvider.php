<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Commands\IndexIntegrity;
use ShabuShabu\ParadeDB\Commands\TestTable;
use ShabuShabu\ParadeDB\Commands\Tokenizers;
use ShabuShabu\ParadeDB\Commands\VersionInfo;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\v1;
use ShabuShabu\ParadeDB\Expressions\v2;
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
            collect([
                ...FullText::cases(),
                ...Distance::cases(),
            ])->map(
                fn (FullText | Distance $operator) => $operator->value
            )->all()
        );

        $this->versionOneMacros();
        $this->versionTwoMacros();
    }

    protected function versionOneMacros(): void
    {
        Builder::macro('selectWithScore', function (array $columns = ['*'], string $key = 'id') {
            return $this->select([...$columns, new v1\Score($key)]);
        });

        Builder::macro('selectWithSnippet', function (string $field, array $columns = ['*'], ?string $startTag = null, ?string $endTag = null, ?int $maxNumChars = null) {
            return $this->select([...$columns, new v1\Snippet($field, $startTag, $endTag, $maxNumChars)]);
        });

        Builder::macro('whereSearch', function (ParadeExpression | Query | string $expression, string $field = 'id') {
            if ($expression instanceof Query) {
                $expression = new v1\Parse($expression);
            }

            return $this->where($field, FullText::search->value, $expression);
        });
    }

    protected function versionTwoMacros(): void
    {
        // Note: can also be used in v1
        Builder::macro('search', function () {
            $scopes = config('paradedb-search.remove_scopes', []);

            return $this->withoutGlobalScopes(
                $scopes[$this->getModel()::class] ?? $scopes['fallback'] ?? null
            );
        });

        Builder::macro('whereQuery', function (string $field, ParadeExpression | Query | string $expression) {
            if ($expression instanceof Query) {
                $expression = new v2\Parse($expression);
            }

            return $this->where($field, FullText::search->value, $expression);
        });

        Builder::macro('whereConjunction', function (string $field, ParadeExpression | string $expression) {
            return $this->where($field, FullText::conjunction->value, $expression);
        });

        Builder::macro('whereDisjunction', function (string $field, ParadeExpression | string $expression) {
            return $this->where($field, FullText::disjunction->value, $expression);
        });

        Builder::macro('wherePhrase', function (string $field, ParadeExpression | string $expression) {
            return $this->where($field, FullText::phrase->value, $expression);
        });

        Builder::macro('whereTerm', function (string $field, ParadeExpression | string | BackedEnum $expression) {
            if ($expression instanceof BackedEnum) {
                $expression = (string) $expression->value;
            }

            return $this->where($field, FullText::term->value, $expression);
        });

        Builder::macro('withScore', function (array $columns = ['*'], string $key = 'id') {
            return $this->select([...$columns, new v2\Score($key)]);
        });

        Builder::macro('orderByScore', function (string $direction = 'desc', array $columns = ['*'], string $key = 'id') {
            return $this
                ->select([...$columns, new v2\Score($key)])
                ->orderBy(new v2\Score($key), $direction);
        });

        Builder::macro('agg', function (string | array $aliases = 'agg'): Collection {
            $aliases = Arr::wrap($aliases);

            return $this->get()->map(function (object $result) use ($aliases) {
                foreach ($aliases as $alias) {
                    data_set($result, $alias, json_decode(data_get($result, $alias), false, 512, JSON_THROW_ON_ERROR));
                }

                return $result;
            });
        });

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
                static fn (string | TokenizerExpression | v2\Support\Type $column) => match (true) {
                    $column instanceof TokenizerExpression => $column->useAsType()->getValue($grammar),
                    $column instanceof v2\Support\Type => $column->getValue($grammar),
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

        Blueprint::macro('bm25', function (array $columns, ?array $parameters = null, ?string $name = null): Fluent {
            $table = $this->table; // @phpstan-ignore-line
            $grammar = $this->grammar; // @phpstan-ignore-line

            $name ??= sprintf('%s_bm25_%s', $table, config('paradedb-search.index_suffix'));
            $columns = array_map(
                static fn (string | TokenizerExpression | v2\Casts\Row $column) => is_string($column)
                    ? $column
                    : Str::wrap($column->getValue($grammar), '(', ')'),
                $columns,
            );

            return $this
                ->index($columns, $name)
                ->algorithm('bm25')
                ->with($parameters ?? ['key_field' => 'id']);
        });
    }
}
