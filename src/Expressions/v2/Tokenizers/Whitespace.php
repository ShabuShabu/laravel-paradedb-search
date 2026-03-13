<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class Whitespace extends BaseTokenizer
{
    public function name(): string
    {
        return 'pdb.whitespace';
    }
}