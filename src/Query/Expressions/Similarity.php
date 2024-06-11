<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Similarity implements Expression
{
    use Stringable;

    public function __construct(
        private string | Expression $column,
        private Distance $operator,
        private array $values,
        private bool $escape = true,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $column = $this->column instanceof Expression
            ? $this->column->getValue($grammar)
            : $this->column;

        $wrap = $this->escape ? "''" : "'";

        $values = Str::wrap(implode(',', $this->values), "{$wrap}[", "]$wrap");

        return "$column {$this->operator->value} $values";
    }
}
