<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions\Ranges;

class Int8 extends Number
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
        return 'int8range';
    }
}
