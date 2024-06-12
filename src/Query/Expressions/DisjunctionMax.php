<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class DisjunctionMax implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array | ParadeExpression | Builder | string $disjuncts,
        private null | int | float $tieBreaker = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $disjuncts = $this->wrapArray(
            $this->normalizeQueries($grammar, $this->disjuncts)
        );

        $tieBreaker = $this->asReal($this->tieBreaker);

        return "paradedb.disjunction_max(disjuncts => $disjuncts, tie_breaker => $tieBreaker)";
    }
}
