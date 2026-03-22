<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1\Inspection;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class IndexInfo implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $index,
        private ?bool $invisible = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'index' => $this->cast($grammar, $this->index),
            'show_invisible' => $this->cast($grammar, $this->invisible),
        ]);

        return "paradedb.index_info($params)";
    }
}
