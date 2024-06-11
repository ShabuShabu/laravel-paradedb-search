<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

enum Distance: string
{
    case l2 = '<->';
    case innerProduct = '<=>';
    case cosine = '<#>';
}
