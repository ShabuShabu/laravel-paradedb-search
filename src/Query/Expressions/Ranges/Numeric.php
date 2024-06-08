<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use Illuminate\Database\Grammar;

readonly class Numeric implements RangeExpression
{
    public function __construct(
        private float $upper,
        private float $lower,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $bounds = $this->bounds->wrap($this->lower, $this->upper);

        return "$bounds::numrange";
    }
}
