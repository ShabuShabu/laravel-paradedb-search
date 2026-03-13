<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\v1\Concerns\Stringable;

final readonly class Tokenize implements Expression
{
    use Stringable;

    public function __construct(
        private Tokenizer $tokenizer,
        private string $input,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $tokenizer = $this->stringize($grammar, $this->tokenizer);
        $input = $grammar->escape($this->input);

        return "paradedb.tokenize($tokenizer, $input)";
    }
}
