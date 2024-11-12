<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\TantivyQL\Operators;

enum Filter: string
{
    use CanQuery;

    case equals = '=';
    case less = '<';
    case lessOrEqual = '<=';
    case greater = '>';
    case greaterOrEqual = '>=';
}
