<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class FuzzyTerm implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $value,
        private null | int | float $distance = null,
        private ?bool $transpose = null,
        private ?bool $prefix = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'value' => $this->cast($grammar, $this->value),
            'distance' => $this->cast($grammar, $this->distance),
            'tranposition_cost_one' => $this->cast($grammar, $this->transpose),
            'prefix' => $this->cast($grammar, $this->prefix),
        ]);

        return "paradedb.fuzzy_term($params)";
    }
}
