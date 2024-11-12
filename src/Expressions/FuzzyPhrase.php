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
        $field = $this->asText($grammar, $this->field);
        $value = $this->asText($grammar, $this->value);
        $distance = $this->asInt($this->distance);
        $transpose = $this->asBool($this->transpose);
        $prefix = $this->asBool($this->prefix);
        $matchAll = $this->asBool($this->matchAll);

        return "paradedb.fuzzy_phrase(field => $field, value => $value, distance => $distance, tranposition_cost_one => $transpose, prefix => $prefix, match_all_terms => $matchAll)";
    }
}
