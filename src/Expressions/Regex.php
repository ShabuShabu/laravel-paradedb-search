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
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'pattern' => $this->cast($grammar, $this->pattern),
        ]);

        return "paradedb.regex($params)";
    }
}
