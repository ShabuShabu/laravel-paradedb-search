<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class PhrasePrefix implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private array $phrases,
        private ?int $maxExpansion = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $maxExpansion = $this->parseInt($this->maxExpansion);
        $phrases = $this->wrapItems($this->phrases);

        return "paradedb.phrase(field => '$this->field', phrases => ARRAY[$phrases], max_expansion => $maxExpansion)";
    }
}
