<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Exists implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
        ]);

        return "paradedb.exists($params)";
    }
}
