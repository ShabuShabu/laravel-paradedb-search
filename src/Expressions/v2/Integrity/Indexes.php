<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Integrity;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Indexes implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return 'pdb.indexes()';
    }
}
