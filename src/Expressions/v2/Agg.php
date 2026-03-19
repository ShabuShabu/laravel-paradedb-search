<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use JsonException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Agg implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $query,
        private bool $visibilityChecks = true,
        private bool $asFacet = false,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        $query = $grammar->escape(
            json_encode($this->query, JSON_THROW_ON_ERROR)
        );

        if (! $this->asFacet) {
            return "pdb.agg($query)";
        }

        $checks = $this->visibilityChecks
            ? ', ' . $this->cast($grammar, $this->visibilityChecks)
            : '';

        return "pdb.agg($query$checks) over ()";
    }
}
