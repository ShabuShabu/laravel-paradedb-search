<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class FullText implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $value,
        private null | string | Tokenizer $tokenizer = null,
        private null | int | float $distance = null,
        private ?bool $transposeCostOne = null,
        private ?bool $prefix = null,
        private ?bool $conjunctionMode = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'value' => $this->cast($grammar, $this->value),
            'tokenizer' => match (true) {
                is_string($this->tokenizer) => $this->stringize($grammar, new Tokenizer($this->tokenizer)),
                $this->tokenizer instanceof Tokenizer => $this->stringize($grammar, $this->tokenizer),
                default => null,
            },
            'distance' => $this->cast($grammar, $this->distance),
            'tranposition_cost_one' => $this->cast($grammar, $this->transposeCostOne),
            'prefix' => $this->cast($grammar, $this->prefix),
            'conjunction_mode' => $this->cast($grammar, $this->conjunctionMode),
        ]);

        return "paradedb.match($params)";
    }
}
