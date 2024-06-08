<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;

readonly class TermSet implements ParadeExpression
{
    public function __construct(
        private array $terms,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $terms = collect($this->terms)
            ->ensure(Term::class)
            ->map(fn (Term $term) => $term->getValue($grammar))
            ->join(', ');

        return "paradedb.term_set(terms => ARRAY[$terms])";
    }
}
