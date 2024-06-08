<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;

readonly class Blank implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return 'paradedb.empty()';
    }
}
