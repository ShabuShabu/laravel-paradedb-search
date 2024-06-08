<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Concerns;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Str;

trait Stringable
{
    protected function toString(Grammar $grammar, string|Expression $expression): float|int|string
    {
        return match ($grammar->isExpression($expression)) {
            true => $grammar->getValue($expression),
            false => $grammar->wrap($expression),
        };
    }

    protected function wrapItems(array $values): string
    {
        return collect($values)
            ->filter(fn (mixed $value) => is_string($value))
            ->map(fn (string $value) => Str::wrap($value, "'"))
            ->join(', ');
    }

    protected function boolToString(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    protected function parseInt(?int $value): string
    {
        return $value === null ? 'NULL::integer' : (string) $value;
    }

    protected function parseReal(?int $value): string
    {
        return $value === null ? 'NULL::real' : (string) $value;
    }

    protected function parseBool(?bool $value): string
    {
        return $value === null ? 'NULL::boolean' : $this->boolToString($value);
    }

    protected function parseText(?string $value): string
    {
        return $value === null ? 'NULL::text' : Str::wrap($value, "'");
    }

    protected function parseDate(string|CarbonInterface $value, string $format): string
    {
        return $value instanceof CarbonInterface ? $value->format($format) : Str::wrap($value, "'");
    }
}
