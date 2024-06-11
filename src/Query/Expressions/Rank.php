<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Rank implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string|Expression $key = 'id',
        private ?string $alias = null
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $key = $grammar->wrap($this->key);
        $alias = $this->parseText($this->alias);

        return "paradedb.rank_bm25(key => $key, alias => $alias)";
    }
}
