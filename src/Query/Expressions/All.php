<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;

readonly class All implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return 'paradedb.all()';
    }
}
