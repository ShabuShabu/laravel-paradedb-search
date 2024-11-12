<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

use Illuminate\Database\Grammar;

abstract class Number implements RangeExpression
{
    protected null | int | float $lower;

    protected null | int | float $upper;

    protected Bounds $bounds;

    abstract protected function castAs(): string;

    public function getValue(Grammar $grammar): string
    {
        if (is_null($this->lower) && is_null($this->upper)) {
            throw InvalidRange::unbounded();
        }

        if ($this->lower && $this->upper && $this->lower >= $this->upper) {
            throw InvalidRange::wrongOrder();
        }

        $bounds = $grammar->escape($this->bounds->wrap(
            $this->lower,
            $this->upper
        ));

        return "$bounds::{$this->castAs()}";
    }
}
