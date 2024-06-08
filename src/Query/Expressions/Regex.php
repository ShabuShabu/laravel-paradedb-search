<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;

readonly class Regex implements ParadeExpression
{
    public function __construct(
        private string $field,
        private string $pattern,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        return "paradedb.regex(field => '$this->field', pattern => '$this->pattern')";
    }
}
