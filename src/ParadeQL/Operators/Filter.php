<?php

namespace ShabuShabu\ParadeDB\ParadeQL\Operators;

enum Filter: string
{
    use CanQuery;

    case equals = '=';
    case less = '<';
    case lessOrEqual = '<=';
    case greater = '>';
    case greaterOrEqual = '>=';
}
