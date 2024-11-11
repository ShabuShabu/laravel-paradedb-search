<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

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
        $field = $this->asText($grammar, $this->field);
        $value = $this->asText($grammar, $this->value);
        $distance = $this->asInt($this->distance);
        $transpose = $this->asBool($this->transpose);
        $prefix = $this->asBool($this->prefix);

        return "paradedb.fuzzy_term(field => $field, value => $value, distance => $distance, tranposition_cost_one => $transpose, prefix => $prefix)";
    }
}
