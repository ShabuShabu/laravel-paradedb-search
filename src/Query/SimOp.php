<?php

namespace ShabuShabu\ParadeDB\Query;

enum SimOp: string
{
    case l2Distance = '<->';
    case innerProduct = '<=>';
    case cosineDistance = '<#>';
}
