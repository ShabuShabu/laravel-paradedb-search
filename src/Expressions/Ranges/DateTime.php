<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

use Carbon\CarbonInterface;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

abstract readonly class DateTime implements RangeExpression
{
    use Stringable;

    public function __construct(
        private null | string | CarbonInterface $lower,
        private null | string | CarbonInterface $upper,
        private Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {}

    abstract protected function format(): string;

    abstract protected function castAs(): string;

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

        $bounds = $grammar->escape($this->bounds->wrap(
            $this->asDate($lower, $this->format()),
            $this->asDate($upper, $this->format()),
        ));

        return "$bounds::{$this->castAs()}";
    }
}
