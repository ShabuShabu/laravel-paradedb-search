<?php

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class Lindera extends BaseTokenizer
{
    public function __construct(
        protected string|Expression $column,
        protected Dictionary $dictionary,
    ) {
        parent::__construct($column);

        $this->parameters = [$this->dictionary->name];
    }

    public function name(): string
    {
        return 'lindera';
    }
}