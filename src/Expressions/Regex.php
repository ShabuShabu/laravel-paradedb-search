<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Regex implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $pattern,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $pattern = $this->asText($grammar, $this->pattern);

        return "paradedb.regex(field => $field, pattern => $pattern)";
    }
}
