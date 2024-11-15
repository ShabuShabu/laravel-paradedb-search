<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

enum Bounds
{
    case includeStartExcludeEnd;
    case includeAll;
    case excludeStartIncludeEnd;
    case excludeAll;

    public function wrap(int | float | string | null $lower, int | float | string | null $upper): string
    {
        $lower ??= 'NULL';
        $upper ??= 'NULL';

        return match ($this) {
            self::includeStartExcludeEnd => "[$lower,$upper)",
            self::includeAll => "[$lower,$upper]",
            self::excludeStartIncludeEnd => "($lower,$upper]",
            self::excludeAll => "($lower,$upper)",
        };
    }
}
