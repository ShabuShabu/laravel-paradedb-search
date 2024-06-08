<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;

readonly class Parse implements ParadeExpression
{
    public function __construct(
        private string|Builder $query
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $query = $this->query instanceof Builder
            ? $this->query->get()
            : $this->query;

        return "paradedb.parse(query_string => '$query')";
    }
}
