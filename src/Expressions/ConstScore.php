<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\TantivyQL\Query;

readonly class ConstScore implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private ParadeExpression | Query | string $query,
        private int | float $score,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'score' => $this->score,
            'query' => $this->normalizeQuery($grammar, $this->query),
        ]);

        return "paradedb.const_score($params)";
    }
}
