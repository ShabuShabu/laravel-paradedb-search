<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use Illuminate\Support\Str;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Rank implements Expression
{
    use Stringable;

    private array $columns;

    public function __construct(array ...$columns)
    {
        $this->columns = $columns;
    }

    public function getValue(Grammar $grammar): string
    {
        $columns = collect($this->columns)
            ->filter(
                fn (array $columns) => isset($columns[0], $columns[1])
                    && ($columns[0] instanceof Expression || is_string($columns[0]))
                    && is_string($columns[1])
            )
            ->map(
                fn (array $columns) => $this->stringize($grammar, $columns[0]) . ' ' . $this->order($columns[1])
            )
            ->values()
            ->implode(', ');

        return "RANK () OVER (ORDER BY $columns)";
    }

    protected function order(string $order): string
    {
        return Str::upper($order) === 'ASC' ? 'ASC' : 'DESC';
    }
}
