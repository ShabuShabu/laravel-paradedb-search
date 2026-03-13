<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class Literal extends BaseTokenizer
{
    protected bool $allowTokenFilters = false;

    public function name(): string
    {
        return 'pdb.literal';
    }
}