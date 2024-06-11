<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class TermSet implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $terms,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $terms = count($this->terms) <= 0
            ? 'ARRAY[]::paradedb.searchqueryinput[]'
            : $this->wrapArray(
                collect($this->terms)
                    ->ensure(Term::class)
                    ->map(fn (Term $term) => $term->getValue($grammar))
            );

        return "paradedb.term_set(terms => $terms)";
    }
}
