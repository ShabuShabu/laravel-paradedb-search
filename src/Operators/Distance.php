<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Operators;

enum Distance: string
{
    case l2 = '<->';
    case innerProduct = '<#>';
    case cosine = '<=>';
    case l1 = '<+>';
    case hamming = '<~>';
    case jaccard = '<%>';
}
