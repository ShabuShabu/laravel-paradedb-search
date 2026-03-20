<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class TextArray implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $tokens,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        return $this->asArray($grammar, $this->tokens);
    }
}
