<?php

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

enum Bounds
{
    case includeStartExcludeEnd;
    case includeAll;
    case excludeStartIncludeEnd;
    case excludeAll;

    public function wrap(int|float|string $lower, int|float|string $upper): string
    {
        return match ($this) {
            self::includeStartExcludeEnd => "[$lower,$upper)",
            self::includeAll => "[$lower,$upper]",
            self::excludeStartIncludeEnd => "($lower,$upper]",
            self::excludeAll => "($lower,$upper)",
        };
    }
}
