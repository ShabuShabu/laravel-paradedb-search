<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Score implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $key = 'id',
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $key = $grammar->wrap($this->key);

        return "pdb.score($key)";
    }
}
