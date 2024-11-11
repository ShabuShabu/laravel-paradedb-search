<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use Illuminate\Support\Arr;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

class DisjunctionMax implements ParadeExpression
{
    use Stringable;

    final public function __construct(
        private array | ParadeExpression | Builder | string $disjuncts,
        private null | int | float $tieBreaker = null,
    ) {}

    public function add(ParadeExpression | Builder | string $query, bool $when = true): static
    {
        if (! is_array($this->disjuncts)) {
            $this->disjuncts = Arr::wrap($this->disjuncts);
        }

        if ($when) {
            $this->disjuncts[] = $query;
        }

        return $this;
    }

    public function tieBreaker(int | float $tieBreaker): static
    {
        $this->tieBreaker = $tieBreaker;

        return $this;
    }

    public function getValue(Grammar $grammar): string
    {
        $disjuncts = $this->wrapArray(
            $this->normalizeQueries($grammar, $this->disjuncts)
        );

        $tieBreaker = $this->asReal($this->tieBreaker);

        return "paradedb.disjunction_max(disjuncts => $disjuncts, tie_breaker => $tieBreaker)";
    }

    public static function query(): static
    {
        return new static([]);
    }
}
