<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;

readonly class Tokenizers implements Expression
{
    public function getValue(Grammar $grammar): string
    {
        return 'paradedb.tokenizers()';
    }
}
