<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use Illuminate\Database\Grammar;
use Illuminate\Contracts\Database\Query\Expression;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Alias implements ParadeExpression
{
    use Stringable;

    public function __construct(
        protected string|Expression $expression,
        protected string $name,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $expression = $this->stringize($grammar, $this->expression);
        $name = $this->toString($grammar, $this->name);

        return "$expression::pdb.alias($name)";
    }
}
