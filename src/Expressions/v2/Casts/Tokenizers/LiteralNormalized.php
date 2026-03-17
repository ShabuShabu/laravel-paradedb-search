<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class LiteralNormalized extends Tokenizer
{
    public function name(): string
    {
        return 'literal_normalized';
    }
}