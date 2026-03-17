<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class Whitespace extends Tokenizer
{
    public function name(): string
    {
        return 'whitespace';
    }
}