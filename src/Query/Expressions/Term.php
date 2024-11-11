<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Term implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string | Expression $value,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $value = $this->toString($grammar, $this->value);

        return "paradedb.term(field => $field, value => $value)";
    }
}
