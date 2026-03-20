<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1\Inspection;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class MergeInfo implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $index,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $index = $this->cast($grammar, $this->index);

        return "paradedb.merge_info($index)";
    }
}
