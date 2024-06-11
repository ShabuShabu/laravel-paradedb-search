<?php

declare(strict_types=1);

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
        $field = $this->parseText($this->field);
        $phrases = $this->parseArray($this->phrases);
        $slop = $this->parseInt($this->slop);

        return "paradedb.phrase(field => $field, phrases => $phrases, slop => $slop)";
    }
}
