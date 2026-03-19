<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use JsonException;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Agg implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $query,
    ) {}

    /**
     * @throws JsonException
     */
    public function getValue(Grammar $grammar): string
    {
        $query = $grammar->escape(
            json_encode($this->query, JSON_THROW_ON_ERROR)
        );

        return "pdb.agg($query)";
    }
}
