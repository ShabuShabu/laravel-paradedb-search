<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class Jieba extends Tokenizer
{
    public function name(): string
    {
        return 'jieba';
    }
}
