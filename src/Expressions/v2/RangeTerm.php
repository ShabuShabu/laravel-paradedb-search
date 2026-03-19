<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use RuntimeException;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Ranges\Relation;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;

final readonly class RangeTerm implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private int | float | string | RangeExpression $term,
        private ?Relation $relation = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if (! $this->relation && $this->isRangeExpression()) {
            throw new RuntimeException('A relation is needed when comparing a range');
        }

        $params = $this->toParams([
            'term' => match (true) {
                $this->isRangeExpression() => $this->term->getValue($grammar),
                default => $this->cast($grammar, $this->term),
            },
            'relation' => $this->isRangeExpression() && $this->relation
                ? $grammar->escape($this->relation->value)
                : null,
        ]);

        return "pdb.range_term($params)";
    }

    protected function isRangeExpression(): bool
    {
        return $this->term instanceof RangeExpression;
    }
}
