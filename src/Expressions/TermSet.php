<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

class TermSet implements ParadeExpression
{
    use Stringable;

    final public function __construct(
        private array $terms,
    ) {}

    public function add(Term $query, bool $when = true): static
    {
        if ($when) {
            $this->terms[] = $query;
        }

        return $this;
    }

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'terms' => count($this->terms) <= 0
                ? null
                : $this->wrapArray(
                    collect($this->terms)
                        ->ensure(Term::class)
                        ->map(fn (Term $term) => $term->getValue($grammar))
                ),
        ]);

        return "paradedb.term_set($params)";
    }

    public static function query(): static
    {
        return new static([]);
    }
}
