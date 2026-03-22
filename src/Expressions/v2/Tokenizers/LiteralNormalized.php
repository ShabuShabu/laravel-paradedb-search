<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class LiteralNormalized extends Tokenizer
{
    public function name(): string
    {
        return 'literal_normalized';
    }
}
