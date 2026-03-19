<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

final class ChineseCompatible extends Tokenizer
{
    public function name(): string
    {
        return 'chinese_compatible';
    }
}
