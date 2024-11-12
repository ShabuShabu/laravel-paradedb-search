<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Phrase implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private array $phrases,
        private ?int $slop = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $phrases = $this->asArray($grammar, $this->phrases);
        $slop = $this->asInt($this->slop);

        return "paradedb.phrase(field => $field, phrases => $phrases, slop => $slop)";
    }
}
