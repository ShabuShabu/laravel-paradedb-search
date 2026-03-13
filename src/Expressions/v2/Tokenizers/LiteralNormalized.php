<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class LiteralNormalized extends BaseTokenizer
{
    public function name(): string
    {
        return 'pdb.literal_normalized';
    }
}