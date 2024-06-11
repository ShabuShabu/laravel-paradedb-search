<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use Illuminate\Database\Grammar;

readonly class Int4 implements RangeExpression
{
    public function __construct(
        private ?int $lower,
        private ?int $upper,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        if (is_null($this->lower) && is_null($this->upper)) {
            throw InvalidRange::unbounded();
        }

        if ($this->lower && $this->upper && $this->lower >= $this->upper) {
            throw InvalidRange::wrongOrder();
        }

        $bounds = $this->bounds->wrap($this->lower, $this->upper);

        return "$bounds::int4range";
    }
}
