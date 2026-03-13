<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

final class SourceCode extends BaseTokenizer
{
    public function name(): string
    {
        return 'pdb.source_code';
    }
}