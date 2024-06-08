<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use Carbon\CarbonInterface;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Date implements RangeExpression
{
    use Stringable;

    public function __construct(
        private string|CarbonInterface $upper,
        private string|CarbonInterface $lower,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $bounds = $this->bounds->wrap(
            $this->parseDate($this->lower, 'Y-m-d'),
            $this->parseDate($this->upper, 'Y-m-d'),
        );

        return "$bounds::daterange";
    }
}
