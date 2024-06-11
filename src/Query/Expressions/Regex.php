<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Regex implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $pattern,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $field = $this->parseText($this->field);
        $pattern = $this->parseText($this->pattern);

        return "paradedb.regex(field => $field, pattern => $pattern)";
    }
}
