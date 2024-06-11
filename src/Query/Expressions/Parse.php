<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Parse implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | Builder $query,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $query = $this->toString($grammar, $this->query);

        return "paradedb.parse(query_string => $query)";
    }
}
