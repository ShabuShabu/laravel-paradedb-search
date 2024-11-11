<?php

declare(strict_types=1);

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
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $range = $this->range->getValue($grammar);

        return "paradedb.range(field => $field, range => $range)";
    }
}
