<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class FullTextSearch implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $index,
        private Builder|ParadeExpression $query,
        private ?int $limit = null,
        private ?int $offset = null,
        private ?string $alias = null,
        private ?bool $stableSort = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $query = $this->query instanceof Builder
            ? $this->query->get()
            : $this->query->getValue($grammar);

        $offset = $this->parseInt($this->offset);
        $limit = $this->parseInt($this->limit);
        $alias = $this->parseText($this->alias);
        $sort = $this->parseBool($this->stableSort);

        return "$this->index.search(query => '$query', offset_rows => $offset, limit_rows => $limit, alias => $alias, stable_sort => $sort)";
    }
}
