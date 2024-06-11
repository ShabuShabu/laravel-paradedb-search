<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class DisjunctionMax implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array|ParadeExpression|Builder|string $disjuncts,
        private null|int|float $tieBreaker = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $tieBreaker = $this->parseReal($this->tieBreaker);

        $disjuncts = $this->wrapArray(
            $this->normalizeQueries($grammar, $this->disjuncts)
        );

        return "paradedb.disjunction_max(disjuncts => $disjuncts, tie_breaker => $tieBreaker)";
    }
}
