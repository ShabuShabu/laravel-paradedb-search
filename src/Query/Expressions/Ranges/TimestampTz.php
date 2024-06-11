<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

use Carbon\CarbonInterface;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class TimestampTz implements RangeExpression
{
    use Stringable;

    public function __construct(
        private null|string|CarbonInterface $lower,
        private null|string|CarbonInterface $upper,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        if (is_null($this->lower) && is_null($this->upper)) {
            throw InvalidRange::unbounded();
        }

        $lower = $this->toDate($this->lower);
        $upper = $this->toDate($this->upper);

        if ($lower && $upper && $lower->gte($upper)) {
            throw InvalidRange::wrongOrder();
        }

        $bounds = $this->bounds->wrap(
            $this->parseDate($lower, 'Y-m-d H:i:sP'),
            $this->parseDate($upper, 'Y-m-d H:i:sP'),
        );

        return "$bounds::tstzrange";
    }
}
