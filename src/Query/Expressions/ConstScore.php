<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class ConstScore implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private ParadeExpression | Builder | string $query,
        private int | float $score,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $query = $this->normalizeQuery($grammar, $this->query);

        return "paradedb.const_score(score => $this->score, query => $query)";
    }
}
