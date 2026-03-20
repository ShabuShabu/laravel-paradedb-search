<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1\Inspection;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class VersionInfo implements ParadeExpression
{
    public function getValue(Grammar $grammar): string
    {
        return 'paradedb.version_info()';
    }
}
