<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class ParseWithField implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $query,
        private ?bool $lenient = null,
        private ?bool $conjunctionMode = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $query = $this->toString($grammar, $this->query);
        $lenient = $this->asBool($this->lenient);
        $conjunctionMode = $this->asBool($this->conjunctionMode);

        return "paradedb.parse_with_field(field => $field, query_string => $query, lenient => $lenient, conjunction_mode => $conjunctionMode)";
    }
}
