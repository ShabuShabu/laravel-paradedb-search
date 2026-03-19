<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

class Int4 extends Number
{
    public function __construct(
        ?int $lower,
        ?int $upper,
        Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
        $this->lower = $lower;
        $this->upper = $upper;
        $this->bounds = $bounds;
    }

    protected function castAs(): string
    {
        return 'int4range';
    }
}
