<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

enum RangeRelation: string
{
    case intersects = 'Intersects';
    case contains = 'Contains';
    case within = 'Within';
}
