<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions;

use Illuminate\Database\Grammar;
use RuntimeException;
use ShabuShabu\ParadeDB\Expressions\Concerns\Stringable;

readonly class Snippet implements ParadeExpression
{
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

    protected function defaultTag(string $type): ?string
    {
        $tags = explode('><', config('paradedb-search.highlighting_tag'));

        if (count($tags) !== 2) {
            throw new RuntimeException('Invalid highlighting tag');
        }

        if ($tags[0] . '>' === '<b>') {
            return null;
        }

        return match ($type) {
            'opening' => $tags[0] . '>',
            'closing' => '<' . $tags[1],
            default => throw new RuntimeException('Undefined snippet type: ' . $type),
        };
    }
}
