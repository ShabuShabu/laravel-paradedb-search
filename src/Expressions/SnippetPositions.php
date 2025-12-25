<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class SnippetPositions implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $column,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'column' => $this->cast($grammar, $this->column),
        ]);

        return "paradedb.snippet_positions($params)";
    }
}
