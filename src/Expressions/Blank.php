<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;

readonly class Blank implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return 'paradedb.empty()';
    }
}
