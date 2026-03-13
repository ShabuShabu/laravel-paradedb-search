<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class ICU extends BaseTokenizer
{
    public function name(): string
    {
        return 'pdb.icu';
    }
}