<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\TantivyQL\Query;

readonly class Parse implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | Query $query,
        private ?bool $lenient = null,
        private ?bool $conjunctionMode = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $query = $this->toString($grammar, $this->query);
        $lenient = $this->asBool($this->lenient);
        $conjunctionMode = $this->asBool($this->conjunctionMode);

        return "paradedb.parse(query_string => $query, lenient => $lenient, conjunction_mode => $conjunctionMode)";
    }
}
