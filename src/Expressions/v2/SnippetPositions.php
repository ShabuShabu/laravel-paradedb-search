<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class SnippetPositions implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private ?int $limit = null,
        private ?int $offset = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->stringize($grammar, $this->field),
            'limit' => $this->cast($grammar, $this->limit),
            'offset' => $this->cast($grammar, $this->offset),
        ]);

        return "pdb.snippet_positions($params)";
    }
}
