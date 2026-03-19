<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class ICU extends Tokenizer
{
    public function name(): string
    {
        return 'icu';
    }
}
