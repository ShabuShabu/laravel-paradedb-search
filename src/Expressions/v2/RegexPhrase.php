<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;

final readonly class RegexPhrase implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $regexes,
        private ?int $slop = null,
        private ?int $maxExpansions = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'regexes' => $this->asArray($grammar, $this->regexes),
            'slop' => $this->cast($grammar, $this->slop),
            'max_expansions' => $this->cast($grammar, $this->maxExpansions),
        ]);

        return "pdb.regex_phrase($params)";
    }
}
