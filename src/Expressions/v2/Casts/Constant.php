<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use InvalidArgumentException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Constant implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | Expression $expression,
        private int $factor,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        if ($this->factor < -2048 || $this->factor > 2048) {
            throw new InvalidArgumentException('Const factor must be between -2048 and 2048.');
        }

        $expression = $this->stringize($grammar, $this->expression);

        return "$expression::pdb.const($this->factor)";
    }
}
