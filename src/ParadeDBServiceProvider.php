<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use ShabuShabu\ParadeDB\Commands\Help;
use ShabuShabu\ParadeDB\Commands\TestTable;
use ShabuShabu\ParadeDB\Commands\Tokenizers;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\v1\Parse;
use ShabuShabu\ParadeDB\Expressions\v1\Score;
use ShabuShabu\ParadeDB\Expressions\v1\Snippet;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Operators\FullText;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tpetry\PostgresqlEnhanced\Query\Grammar;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\TokenizerExpression;

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
            ->merge(
                collect(FullText::cases())
                    ->map(fn (FullText $operator) => $operator->value)
            )
            ->all();

        Grammar::customOperators($operators);
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

        Blueprint::macro('bm25', function (string $name, array $columns, ?array $parameters = null) {
            $grammar = $this->grammar; // @phpstan-ignore-line
            $columns = array_map(
                static fn (string|TokenizerExpression $column) => $column instanceof TokenizerExpression
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
