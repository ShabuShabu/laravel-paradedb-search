<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class HybridSearch implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $index,
        private string | Builder | ParadeExpression $bm25Query,
        private Similarity $similarityQuery,
        private int $bm25Limit = 100,
        private float | int $bm25Weight = 0.5,
        private int $similarityLimit = 100,
        private float | int $similarityWeight = 0.5,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $bm25Query = $this->normalizeQuery($grammar, $this->bm25Query);
        $similarityQuery = $grammar->escape($this->similarityQuery->getValue($grammar));

        return "$this->index.rank_hybrid(bm25_query => $bm25Query, similarity_query => $similarityQuery, bm25_weight => $this->bm25Weight, similarity_weight => $this->similarityWeight, bm25_limit_n => $this->bm25Limit, similarity_limit_n => $this->similarityLimit)";
    }
}
