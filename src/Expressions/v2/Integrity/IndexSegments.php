<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Integrity;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class IndexSegments implements ParadeExpression
{
    use Stringable;

    public function __construct(
        protected string $index,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $index = $this->toString($grammar, $this->index);

        return "pdb.index_segments($index)";
    }
}
