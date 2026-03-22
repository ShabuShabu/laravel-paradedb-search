<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class RegexPattern extends Tokenizer
{
    public function __construct(
        string | Expression $column,
        string $pattern,
    ) {
        parent::__construct($column);

        $this->parameters = ["'$pattern'"];
    }

    public function name(): string
    {
        return 'regex_pattern';
    }
}
