<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use Illuminate\Database\Grammar;

readonly class Int4 implements RangeExpression
{
    public function __construct(
        private int $upper,
        private int $lower,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $bounds = $this->bounds->wrap($this->lower, $this->upper);

        return "$bounds::int4range";
    }
}
