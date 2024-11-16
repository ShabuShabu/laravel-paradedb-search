<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use RuntimeException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;
use ShabuShabu\ParadeDB\Expressions\Ranges\Relation;

readonly class RangeTerm implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private int | float | string | RangeExpression $term,
        private ?Relation $relation = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if (! $this->relation && $this->isRangeExpression()) {
            throw new RuntimeException('A relation is needed when comparing a range');
        }

        $field = $this->asText($grammar, $this->field);
        $term = match (true) {
            is_int($this->term) => $this->asInt($this->term),
            is_float($this->term) => $this->asReal($this->term),
            is_string($this->term) => $this->asText($grammar, $this->term),
            default => $this->term->getValue($grammar),
        };

        return $this->isRangeExpression() && $this->relation
            ? "paradedb.range_term(field => $field, term => $term, relation => '{$this->relation->value}')"
            : "paradedb.range_term(field => $field, term => $term)";
    }

    protected function isRangeExpression(): bool
    {
        return $this->term instanceof RangeExpression;
    }
}
