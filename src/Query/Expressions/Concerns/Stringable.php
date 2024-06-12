<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Query\Expressions\Parse;

trait Stringable
{
    protected function toString(Grammar $grammar, string | Expression | Builder $query): float | int | string
    {
        return match (true) {
            is_string($query) => $grammar->escape($query),
            $query instanceof Builder => $grammar->escape($query->get()),
            $query instanceof Expression => $grammar->getValue($query),
        };
    }

    protected function normalizeQuery(Grammar $grammar, ParadeExpression | Builder | string $query): ?string
    {
        return match (true) {
            is_string($query),
            $query instanceof Builder => $this->toString($grammar, new Parse($query)),
            $query instanceof ParadeExpression => $this->toString($grammar, $query),
        };
    }

    protected function normalizeQueries(Grammar $grammar, ParadeExpression | Builder | string | array $queries): Collection
    {
        return collect(Arr::wrap($queries))
            ->map(fn (mixed $query) => $this->normalizeQuery($grammar, $query))
            ->filter()
            ->values();
    }

    protected function asArray(Grammar $grammar, array $values): string
    {
        return $this->wrapArray(
            collect($values)
                ->filter(fn (mixed $value) => is_string($value))
                ->map(fn (string $value) => $grammar->escape($value))
        );
    }

    protected function wrapArray(Collection $values): string
    {
        return Str::wrap($values->join(', '), 'ARRAY[', ']');
    }

    protected function toDate(CarbonInterface | string | null $value): ?CarbonInterface
    {
        if (is_null($value)) {
            return null;
        }

        return $value instanceof CarbonInterface ? $value : Date::parse($value);
    }

    protected function asDate(Grammar $grammar, ?CarbonInterface $value, string $format): string
    {
        return ! is_null($value)
            ? $grammar->escape($value->format($format))
            : '';
    }

    protected function asBool(?bool $value): string
    {
        return match ($value) {
            null => 'NULL::boolean',
            true => 'true',
            false => 'false',
        };
    }

    protected function asInt(?int $value): string
    {
        return $value === null ? 'NULL::integer' : (string) $value;
    }

    protected function asReal(null | int | float $value): string
    {
        return $value === null ? 'NULL::real' : (string) $value;
    }

    protected function asText(Grammar $grammar, ?string $value): string
    {
        return $value === null ? 'NULL::text' : $grammar->escape($value);
    }
}
