<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;

readonly class Range implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private RangeExpression $range,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'range' => $this->range->getValue($grammar),
        ]);

        return "paradedb.range($params)";
    }
}
