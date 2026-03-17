<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class SourceCode extends Tokenizer
{
    public function name(): string
    {
        return 'source_code';
    }
}