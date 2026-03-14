<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class Jieba extends BaseTokenizer
{
    public function name(): string
    {
        return 'jieba';
    }
}