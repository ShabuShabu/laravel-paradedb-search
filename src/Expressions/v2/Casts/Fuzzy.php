<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Fuzzy implements TypeExpression
{
    use Stringable;

    public function __construct(
        private string | Expression $expression,
        private int $distance,
        private bool $prefix = false,
        private bool $transposition = false,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if ($this->distance < 0 || $this->distance > 2) {
            throw new InvalidArgumentException('Edit distance must be between 0 and 2.');
        }

        $expression = $this->stringize($grammar, $this->expression);

        $distance = $this->cast($grammar, $this->distance);
        $prefix = $this->bool($this->prefix);
        $transposition = $this->bool($this->transposition);

        return "$expression::pdb.fuzzy($distance, $prefix, $transposition)";
    }
}
