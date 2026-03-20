<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Types;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Constant implements TypeExpression
{
    use Stringable;

    public function __construct(
        private string | Expression $expression,
        private int $factor,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $expression = $this->stringize($grammar, $this->expression);

        return "$expression::pdb.const($this->factor)";
    }
}
