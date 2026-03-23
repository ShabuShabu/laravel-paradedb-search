<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Support;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

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
