<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use JsonException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Operators\Distance;

readonly class Similarity implements Expression
{
    use Stringable;

    public function __construct(
        private string | Expression $column,
        private Distance $operator,
        private array | string | Expression $values,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        $column = $this->stringize($grammar, $this->column);
        $values = $this->formatValues($grammar, $this->values);

        return "$column {$this->operator->value} $values";
    }

    /**
     * @throws JsonException
     */
    protected function formatValues(Grammar $grammar, array | string | Expression $values): string
    {
        if ($values instanceof Expression) {
            return $this->stringize($grammar, $values);
        }

        if (is_string($values)) {
            $values = json_decode($values, true, 512, JSON_THROW_ON_ERROR);
        }

        if (! array_is_list($values)) {
            throw new InvalidArgumentException('Expected similarity values to be a list');
        }

        return $grammar->escape(
            json_encode($values, JSON_THROW_ON_ERROR, 1)
        );
    }
}
