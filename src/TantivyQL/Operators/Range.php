<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\TantivyQL\Operators;

enum Range: string
{
    use CanQuery;

    case includeStartExcludeEnd = '[}';
    case includeAll = '[]';
    case excludeStartIncludeEnd = '{]';
    case excludeAll = '{}';

    public static function isInvalidFilter(mixed $value): bool
    {
        return is_array($value) && (
            count($value) > 2 ||
            ! is_int($value[0]) ||
            ! is_int($value[1]) ||
            $value[0] >= $value[1]
        );
    }
}
