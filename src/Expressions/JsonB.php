<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use JsonException;

readonly class JsonB implements ParadeExpression
{
    public function __construct(
        private array | string $query,
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
            is_string($this->query)
                ? $this->query
                : json_encode($this->query, JSON_THROW_ON_ERROR)
        );

        return "$query::jsonb";
    }
}
