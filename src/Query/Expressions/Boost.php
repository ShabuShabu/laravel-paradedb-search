<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Boost implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private ParadeExpression $query,
        private int|float $boostBy
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $query = $this->toString($grammar, $this->query);

        return "paradedb.boost(boost => $this->boostBy, query => $query)";
    }
}
