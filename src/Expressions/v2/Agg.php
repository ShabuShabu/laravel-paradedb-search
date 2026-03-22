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
        private string | array $query,
        private bool $visibilityChecks = true,
        private bool $asFacet = false,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        if (is_string($this->query) && ! json_validate($this->query)) {
            throw new JsonException('Invalid JSON query');
        }

        $query = $grammar->escape(
            json_encode(
                is_string($this->query) ? json_decode($this->query, true, 512, JSON_THROW_ON_ERROR) : $this->query,
                JSON_THROW_ON_ERROR,
            )
        );

        $checks = ! $this->visibilityChecks
            ? ', ' . $this->cast($grammar, $this->visibilityChecks)
            : '';

        return $this->asFacet
            ? "pdb.agg($query$checks) over ()"
            : "pdb.agg($query$checks)";
    }
}
