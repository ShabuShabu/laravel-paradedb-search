<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v2;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class PhrasePrefix implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private array $phrases,
        private ?int $maxExpansion = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'phrases' => $this->asArray($grammar, $this->phrases),
            'max_expansion' => $this->cast($grammar, $this->maxExpansion),
        ]);

        return "pdb.phrase_prefix($params)";
    }
}
