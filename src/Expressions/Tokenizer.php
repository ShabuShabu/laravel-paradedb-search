<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Tokenizer implements Expression
{
    use Stringable;

    final public function __construct(
        private string $name,
        private ?int $removeLong = null,
        private ?bool $lowercase = null,
        private ?int $minGram = null,
        private ?int $maxGram = null,
        private ?bool $prefixOnly = null,
        private ?string $language = null,
        private ?string $pattern = null,
        private ?string $stemmer = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'name' => $this->cast($grammar, $this->name),
            'remove_long' => $this->cast($grammar, $this->removeLong),
            'lowercase' => $this->cast($grammar, $this->lowercase),
            'min_gram' => $this->cast($grammar, $this->minGram),
            'max_gram' => $this->cast($grammar, $this->maxGram),
            'prefix_only' => $this->cast($grammar, $this->prefixOnly),
            'language' => $this->cast($grammar, $this->language),
            'pattern' => $this->cast($grammar, $this->pattern),
            'stemmer' => $this->cast($grammar, $this->stemmer),
        ]);

        return "paradedb.tokenizer($params)";
    }
}
