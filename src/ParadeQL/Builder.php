<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\ParadeQL;

use BackedEnum;
use Closure;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\ParadeQL\Operators\Filter;
use ShabuShabu\ParadeDB\ParadeQL\Operators\Range;

class Builder
{
    protected array $wheres = [];

    protected array $specialChars = [
        '\\', '+', '^', '`', ':',
        '{', '}', '"', '[', ']',
        '(', ')', '~', '!', '*',
    ];

    public static function make(): static
    {
        /** @phpstan-ignore-next-line */
        return new static;
    }

    public function where(Closure | string $column, null | int | string | array $value = null, ?int $boost = null, ?int $slop = null, string $boolean = 'AND'): static
    {
        if (! $column instanceof Closure && is_array($value)) {
            return $this->whereIn($column, $value, $boost, $boolean);
        }

        $value = $this->prepareValue($value, $slop);

        $this->wheres[] = compact('column', 'value', 'boost', 'boolean');

        return $this;
    }

    public function orWhere(Closure | string $column, null | int | string | array $value = null, ?int $boost = null, ?int $slop = null): static
    {
        return $this->where($column, $value, $boost, $slop, 'OR');
    }

    protected function whereIn(string $column, array $values, ?int $boost = null, string $boolean = 'AND'): static
    {
        $values = array_map(
            fn (string $value) => $this->prepareValue($value),
            array_filter($values, static fn (mixed $value) => is_string($value))
        );

        $this->wheres[] = compact('column', 'values', 'boost', 'boolean');

        return $this;
    }

    public function whereNot(Closure | string $column, null | int | string $value = null, ?int $boost = null, ?int $slop = null, string $boolean = 'AND'): static
    {
        return $this->where($column, $value, $boost, $slop, $boolean . ' NOT');
    }

    public function orWhereNot(Closure | string $column, null | int | string $value = null, ?int $boost = null, ?int $slop = null): static
    {
        return $this->whereNot($column, $value, $boost, $slop, 'OR');
    }

    public function whereFilter(string $column, string | Range | Filter $operator, bool | int | string | array $value, ?int $boost = null, string $boolean = 'AND'): static
    {
        $this->assertFilterOperator($operator, $value);
        $this->assertRangeOperator($operator, $value);
        $this->assertRangeFilter($value);

        $this->wheres[] = compact('column', 'operator', 'value', 'boost', 'boolean');

        return $this;
    }

    public function orWhereFilter(string $column, string | Range | Filter $operator, bool | int | array $value, ?int $boost = null): static
    {
        return $this->whereFilter($column, $operator, $value, $boost, 'OR');
    }

    public function get(): string
    {
        return collect($this->wheres)
            ->map(fn (array $where, int $index) => match (true) {
                isset($where['operator']) => $this->compileFilterQuery($where, $index),
                isset($where['values']) => $this->compileContainsQuery($where, $index),
                default => $this->compileWhereQuery($where, $index),
            })
            ->implode(' ');
    }

    protected function compileWhereQuery(array $where, int $index): string
    {
        [
            'column' => $column,
            'value' => $value,
            'boost' => $boost,
            'boolean' => $boolean,
        ] = $where;

        if ($column instanceof Closure) {
            return $this->boolean($boolean, $index) . Str::wrap(
                $column(static::make())->get(),
                '(',
                ')'
            );
        }

        return $this->boolean($boolean, $index) . "$column:$value" . $this->boost($boost);
    }

    protected function compileContainsQuery(array $where, int $index): string
    {
        [
            'column' => $column,
            'values' => $values,
            'boost' => $boost,
            'boolean' => $boolean,
        ] = $where;

        $values = implode(', ', $values);

        return $this->boolean($boolean, $index) . "$column:IN [$values]" . $this->boost($boost);
    }

    protected function compileFilterQuery(array $where, int $index): string
    {
        [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boost' => $boost,
            'boolean' => $boolean,
        ] = $where;

        if ($operator instanceof BackedEnum) {
            $operator = $operator->value;
        }

        $query = match (true) {
            is_bool($value) => "$column:" . ($value ? 'true' : 'false'),
            is_array($value) && $operator === Range::includeAll->value => sprintf('%s:[%d TO %d]', $column, $value[0], $value[1]),
            is_array($value) && $operator === Range::excludeAll->value => sprintf('%s:{%d TO %d}', $column, $value[0], $value[1]),
            $operator !== '=' => "$column:$operator$value",
            default => "$column:$value",
        };

        return $this->boolean($boolean, $index) . $query . $this->boost($boost);
    }

    protected function assertRangeOperator(mixed $operator, mixed $value): void
    {
        if (is_array($value) && ! Range::contains($operator)) {
            throw InvalidFilter::unknownRangeOperator($operator);
        }
    }

    protected function assertFilterOperator(mixed $operator, mixed $value): void
    {
        if (! is_array($value) && ! Filter::contains($operator)) {
            throw InvalidFilter::unknownFilterOperator($operator);
        }
    }

    protected function assertRangeFilter(mixed $value): void
    {
        if (Range::isInvalidFilter($value)) {
            throw InvalidFilter::malformedRange($value);
        }
    }

    protected function boolean(string $boolean, int $index): string
    {
        return $index > 0 ? "$boolean " : '';
    }

    protected function boost(?int $boost): string
    {
        return $boost ? "^$boost" : '';
    }

    protected function slop(?int $slop): string
    {
        return $slop ? "~$slop" : '';
    }

    protected function prepareValue(?string $value, ?int $slop = null): ?string
    {
        if (! $value) {
            return $value;
        }

        $replacements = array_map(
            static fn (string $char) => "\\$char",
            $this->specialChars
        );

        $value = str_replace($this->specialChars, $replacements, trim($value));

        if (str_contains($value, ' ')) {
            $value = Str::wrap($value, '"') . $this->slop($slop);
        }

        return $value;
    }
}
