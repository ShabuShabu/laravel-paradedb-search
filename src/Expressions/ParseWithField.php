<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class ParseWithField implements ParadeExpression
{
    use Stringable;

    public function __construct(
        private string $field,
        private string $query,
        private ?bool $lenient = null,
        private ?bool $conjunctionMode = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->cast($grammar, $this->field),
            'query_string' => $this->toString($grammar, $this->query),
            'lenient' => $this->cast($grammar, $this->lenient),
            'conjunction_mode' => $this->cast($grammar, $this->conjunctionMode),
        ]);

        return "paradedb.parse_with_field($params)";
    }
}
