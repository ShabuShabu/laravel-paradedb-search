<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class FullTextSearch implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $index,
        private string | Builder | ParadeExpression $query,
        private ?int $limit = null,
        private ?int $offset = null,
        private ?string $alias = null,
        private ?bool $stableSort = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $query = $this->normalizeQuery($grammar, $this->query);
        $limit = $this->asInt($this->limit);
        $offset = $this->asInt($this->offset);
        $alias = $this->asText($grammar, $this->alias);
        $sort = $this->asBool($this->stableSort);

        return "$this->index.search(query => $query, offset_rows => $offset, limit_rows => $limit, alias => $alias, stable_sort => $sort)";
    }
}
