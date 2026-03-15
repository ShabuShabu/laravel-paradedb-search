<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Boost implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private ParadeExpression | Query | string $query,
        private int | float $boost,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'factor' => $this->boost,
            'query' => $this->normalizeQuery($grammar, $this->query),
        ]);

        return "paradedb.boost($params)";
    }
}
