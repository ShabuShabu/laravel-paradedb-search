<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1\Ranges;

enum Relation: string
{
    case intersects = 'Intersects';
    case contains = 'Contains';
    case within = 'Within';
}
