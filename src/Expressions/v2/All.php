<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class All implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return '';
    }
}
