<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class Literal extends Tokenizer
{
    protected bool $allowTokenFilters = false;

    public function name(): string
    {
        return 'literal';
    }
}
