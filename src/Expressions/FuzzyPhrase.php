<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class FuzzyPhrase implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $value,
        private null | int | float $distance = null,
        private ?bool $transpose = null,
        private ?bool $prefix = null,
        private ?bool $matchAll = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'value' => $this->cast($grammar, $this->value),
            'distance' => $this->cast($grammar, $this->distance),
            'tranposition_cost_one' => $this->cast($grammar, $this->transpose),
            'prefix' => $this->cast($grammar, $this->prefix),
            'match_all_terms' => $this->cast($grammar, $this->matchAll),
        ]);

        return "paradedb.fuzzy_phrase($params)";
    }
}
