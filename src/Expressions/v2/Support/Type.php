<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Support;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Type implements Expression
{
    use Stringable;

    public function __construct(
        private string $column,
        private string $type,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $column = $grammar->wrap($this->column);
        $type = Str::lower($this->type);

        return "$column $type";
    }
}
