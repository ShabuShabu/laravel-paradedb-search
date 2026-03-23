<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Row implements Expression
{
    use Stringable;

    public function __construct(
        private string $type,
        private array $columns,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $columns = collect($this->columns)
            ->filter(fn (mixed $column) => is_string($column))
            ->map(fn (string $column) => $grammar->wrap($column))
            ->implode(', ');

        return "ROW($columns)::$this->type";
    }
}
