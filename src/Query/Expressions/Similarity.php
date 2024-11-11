<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use JsonException;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Similarity implements Expression
{
    use Stringable;

    public function __construct(
        private string | Expression $column,
        private Distance $operator,
        private array | string $values,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        $column = $this->column instanceof Expression
            ? $this->column->getValue($grammar)
            : $grammar->wrap($this->column);

        $values = $this->formatValues($grammar, $this->values);

        return "$column {$this->operator->value} $values";
    }

    /**
     * @throws JsonException
     */
    protected function formatValues(Grammar $grammar, array | string $values): string
    {
        if (is_string($values)) {
            $values = json_decode($this->values, true, 2, JSON_THROW_ON_ERROR);
        }

        if (! array_is_list($values)) {
            throw new InvalidArgumentException('Expected similarity values to be a list');
        }

        return $grammar->escape(
            json_encode($values, JSON_THROW_ON_ERROR, 1)
        );
    }
}
