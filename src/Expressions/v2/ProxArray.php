<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class ProxArray implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $clauses,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $clauses = collect($this->clauses)
            ->filter(fn (mixed $clause) => is_string($clause) || $clause instanceof ProxRegex)
            ->map(fn (string | ProxRegex $clause) => $this->stringize($grammar, $clause))
            ->join(', ');

        return "pdb.prox_array($clauses)";
    }
}
