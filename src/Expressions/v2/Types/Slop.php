<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Types;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Slop implements TypeExpression
{
    use Stringable;

    public function __construct(
        private array | string | Expression $expression,
        private int $distance,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if ($this->distance < 1) {
            throw new InvalidArgumentException('Slop distance must be greater than 0.');
        }

        $expression = is_array($this->expression)
            ? $this->asArray($grammar, $this->expression)
            : $this->stringize($grammar, $this->expression);

        return "$expression::pdb.slop($this->distance)";
    }
}
