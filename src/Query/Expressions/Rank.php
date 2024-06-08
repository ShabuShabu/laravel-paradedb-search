<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Rank implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $key = 'id',
        private ?string $alias = null
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $alias = $this->parseText($this->alias);

        return "paradedb.rank_bm25(key => '$this->key', alias => $alias)";
    }
}
