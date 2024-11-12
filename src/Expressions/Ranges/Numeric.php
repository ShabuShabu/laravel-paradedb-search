<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Ranges;

class Numeric extends Number
{
    public function __construct(
        ?float $lower,
        ?float $upper,
        Bounds $bounds = Bounds::excludeStartIncludeEnd
    ) {
        $this->lower = $lower;
        $this->upper = $upper;
        $this->bounds = $bounds;
    }

    protected function castAs(): string
    {
        return 'numrange';
    }
}
