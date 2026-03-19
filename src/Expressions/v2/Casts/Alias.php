<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2\Casts;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class Alias implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | Expression $expression,
        private string $name,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $expression = $this->stringize($grammar, $this->expression);
        $name = $this->toString($grammar, $this->name);

        return "$expression::pdb.alias($name)";
    }
}
