<?php

namespace ShabuShabu\ParadeDB\Query\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Query\Expressions\Concerns\Stringable;

readonly class Highlight implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private int $key,
        private string $field,
        private ?string $prefix = null,
        private ?string $postfix = null,
        private ?int $maxNumChars = null,
        private ?string $alias = null,
    ) {
    }

    public function getValue(Grammar $grammar): string
    {
        $prefix = $this->parseText($this->prefix);
        $postfix = $this->parseText($this->postfix);
        $maxNumChars = $this->parseInt($this->maxNumChars);
        $alias = $this->parseText($this->alias);

        return "paradedb.highlight(key => $this->key, field => '$this->field', prefix => $prefix, postfix => $postfix, max_num_chars => $maxNumChars, alias => $alias)";
    }
}
