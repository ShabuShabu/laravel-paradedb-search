<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class ProxRegex implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $regex,
        private ?int $maxExpansions = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'regex' => $this->cast($grammar, $this->regex),
            'max_expansions' => $this->cast($grammar, $this->maxExpansions),
        ]);

        return "pdb.prox_regex($params)";
    }
}
