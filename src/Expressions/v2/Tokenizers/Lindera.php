<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class Lindera extends Tokenizer
{
    public function __construct(
        string | Expression $column,
        Dictionary $dictionary,
    ) {
        parent::__construct($column);

        $this->parameters = [$dictionary->name];
    }

    public function name(): string
    {
        return 'lindera';
    }
}
