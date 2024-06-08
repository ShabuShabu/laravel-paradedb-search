<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use Illuminate\Support\Arr;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class DisjunctionMax implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array|ParadeExpression $disjuncts,
        private null|int|float $tieBreaker = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $tieBreaker = $this->parseReal($this->tieBreaker);

        $disjuncts = collect(Arr::wrap($this->disjuncts))
            ->ensure(ParadeExpression::class)
            ->map(fn (ParadeExpression $disjunct) => $this->toString($grammar, $disjunct))
            ->join(', ');

        return "paradedb.disjunction_max(query => ARRAY[$disjuncts], tie_breaker => $tieBreaker)";
    }
}
