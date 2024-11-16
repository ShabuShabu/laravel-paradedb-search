<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Score implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $key = 'id',
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $key = $grammar->wrap($this->key);

        return "paradedb.score($key)";
    }
}
