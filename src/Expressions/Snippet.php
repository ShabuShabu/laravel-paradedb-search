<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Snippet implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private ?string $startTag = null,
        private ?string $endTag = null,
        private int $maxNumChars = 150,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $startTag = $this->asText($grammar, $this->startTag);
        $endTag = $this->asText($grammar, $this->endTag);
        $maxNumChars = $this->asInt($this->maxNumChars);

        return "paradedb.snippet(field => $field, start_tag => $startTag, end_tag => $endTag, max_num_chars => $maxNumChars)";
    }
}
