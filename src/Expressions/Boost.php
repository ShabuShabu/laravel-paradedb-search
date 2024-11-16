<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\TantivyQL\Query;

readonly class Boost implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private ParadeExpression | Query | string $query,
        private int | float $boost,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'boost' => $this->boost,
            'query' => $this->normalizeQuery($grammar, $this->query),
        ]);

        return "paradedb.boost($params)";
    }
}
