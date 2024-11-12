<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use JsonException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Json implements ParadeExpression
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

        return "'$query'::jsonb";
    }
}
