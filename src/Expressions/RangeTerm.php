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

        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'term' => match (true) {
                $this->isRangeExpression() => $this->term->getValue($grammar),
                default => $this->cast($grammar, $this->term),
            },
            'relation' => $this->isRangeExpression() && $this->relation
                ? "'{$this->relation->value}'"
                : null,
        ]);

        return "paradedb.range_term($params)";
    }

    protected function isRangeExpression(): bool
    {
        return $this->term instanceof RangeExpression;
    }
}
