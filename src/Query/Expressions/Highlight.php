<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Highlight implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $key,
        private string $field,
        private ?string $prefix = null,
        private ?string $postfix = null,
        private ?int $maxNumChars = null,
        private ?string $alias = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $key = $grammar->wrap($this->key);
        $field = $this->asText($grammar, $this->field);
        $prefix = $this->asText($grammar, $this->prefix);
        $postfix = $this->asText($grammar, $this->postfix);
        $maxNumChars = $this->asInt($this->maxNumChars);
        $alias = $this->asText($grammar, $this->alias);

        return "paradedb.highlight(key => $key, field => $field, prefix => $prefix, postfix => $postfix, max_num_chars => $maxNumChars, alias => $alias)";
    }
}
