<?php

namespace ShabuShabu\ParadeDB\Query;

enum Distance: string
{
    case l2 = '<->';
    case innerProduct = '<=>';
    case cosine = '<#>';
}
