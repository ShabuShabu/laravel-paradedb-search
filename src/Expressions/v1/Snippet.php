<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\v1;

use RuntimeException;
use Illuminate\Database\Grammar;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Concerns\Taggable;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

final readonly class Snippet implements ParadeExpression
{
    use Taggable;
    use Stringable;

    public function __construct(
        private string $field,
        private ?string $startTag = null,
        private ?string $endTag = null,
        private ?int $maxNumChars = null,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $params = $this->toParams([
            'field' => $this->stringize($grammar, $this->field),
            'start_tag' => $this->cast($grammar, $this->startTag ?? $this->defaultTag('opening')),
            'end_tag' => $this->cast($grammar, $this->endTag ?? $this->defaultTag('closing')),
            'max_num_chars' => $this->cast($grammar, $this->maxNumChars),
        ]);

        return "paradedb.snippet($params)";
    }
}
