<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class PhrasePrefix implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private array $phrases,
        private ?int $expansion = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $field = $this->parseText($this->field);
        $phrases = $this->parseArray($this->phrases);
        $expansion = $this->parseInt($this->expansion);

        return "paradedb.phrase_prefix(field => $field, phrases => $phrases, max_expansion => $expansion)";
    }
}
