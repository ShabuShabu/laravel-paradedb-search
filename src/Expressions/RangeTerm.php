<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;

readonly class RangeTerm implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private int | float | string | RangeExpression $term,
        private ?RangeRelation $relation = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $term = match (true) {
            is_int($this->term) => $this->asInt($this->term),
            is_float($this->term) => $this->asReal($this->term),
            is_string($this->term) => $this->asText($grammar, $this->term),
            $this->term instanceof RangeExpression => $this->term->getValue($grammar),
        };

        if ($this->term instanceof RangeExpression && $this->relation) {
            return "paradedb.range_term(field => $field, term => $term, relation => '{$this->relation->value}')";
        }

        return "paradedb.range_term(field => $field, term => $term)";
    }
}
