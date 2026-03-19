<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use InvalidArgumentException;
use Illuminate\Database\Grammar;
use Illuminate\Contracts\Database\Query\Expression;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Slop implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string|Expression $expression,
        private int $distance,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if ($this->distance < 1) {
            throw new InvalidArgumentException('Slop distance must be greater than 0.');
        }

        $expression = $this->stringize($grammar, $this->expression);

        return "$expression::pdb.slop($this->distance)";
    }
}
