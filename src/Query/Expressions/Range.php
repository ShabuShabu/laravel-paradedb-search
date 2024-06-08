<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\RangeExpression;

readonly class Range implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private RangeExpression $range,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $range = $this->range->getValue($grammar);

        return "paradedb.range(field => '$this->field', range => $range)";
    }
}
