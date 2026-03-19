<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers;

use Illuminate\Contracts\Database\Query\Expression;

final class RegexPattern extends Tokenizer
{
    public function __construct(
        protected string | Expression $column,
        protected string $pattern,
    ) {
        parent::__construct($column);

        $this->parameters = ["'$this->pattern'"];
    }

    public function name(): string
    {
        return 'regex_pattern';
    }
}
