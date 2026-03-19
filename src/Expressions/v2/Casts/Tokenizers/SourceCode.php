<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class SourceCode extends Tokenizer
{
    public function name(): string
    {
        return 'source_code';
    }
}
