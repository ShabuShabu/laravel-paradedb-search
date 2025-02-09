<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Tokenize implements Expression
{
    use Stringable;

    final public function __construct(
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
