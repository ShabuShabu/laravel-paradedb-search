<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Proximity implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | ProxRegex | ProxArray $token1,
        private int $distance,
        private string $token2,
        private bool $enforceOrder = false,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $operator = $this->enforceOrder ? '##>' : '##';
        $token1 = $this->stringize($grammar, $this->token1);
        $distance = $this->cast($grammar, $this->distance);
        $token2 = $this->cast($grammar, $this->token2);

        return "($token1 $operator $distance $operator $token2)";
    }
}
