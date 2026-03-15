<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Parse implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string | Query $query,
        private ?bool $lenient = null,
        private ?bool $conjunctionMode = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'query_string' => $this->toString($grammar, $this->query),
            'lenient' => $this->cast($grammar, $this->lenient),
            'conjunction_mode' => $this->cast($grammar, $this->conjunctionMode),
        ]);

        return "paradedb.parse($params)";
    }
}
