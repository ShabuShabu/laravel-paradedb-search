<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class FuzzyTerm implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $value,
        private null|int|float $distance = null,
        private ?bool $transpose = null,
        private ?bool $prefix = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $distance = $this->parseInt($this->distance);
        $transpose = $this->parseBool($this->transpose);
        $prefix = $this->parseBool($this->prefix);

        return "paradedb.fuzzy_term(field => '$this->field', value => '$this->value', distance => $distance, transpose_cost_one => $transpose, prefix => $prefix)";
    }
}
