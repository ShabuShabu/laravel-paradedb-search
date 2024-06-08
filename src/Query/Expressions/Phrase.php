<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Phrase implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private array $phrases,
        private ?int $slop = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $phrases = $this->wrapItems($this->phrases);
        $slop = $this->parseInt($this->slop);

        return "paradedb.phrase(field => '$this->field', phrases => ARRAY[$phrases], slop => $slop)";
    }
}
