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
        private int $maxNumChars = 150,
    ) {}

    public function getValue(Grammar $grammar): string
    {
        $field = $this->asText($grammar, $this->field);
        $startTag = $this->asText($grammar, $this->startTag ?? $this->defaultTag('opening'));
        $endTag = $this->asText($grammar, $this->endTag ?? $this->defaultTag('closing'));
        $maxNumChars = $this->asInt($this->maxNumChars);

        return "paradedb.snippet(field => $field, start_tag => $startTag, end_tag => $endTag, max_num_chars => $maxNumChars)";
    }

    protected function defaultTag(string $type): string
    {
        $tags = explode('><', config('paradedb-search.highlighting_tag'));

        if (count($tags) !== 2) {
            throw new RuntimeException('Invalid highlighting tag');
        }

        return match ($type) {
            'opening' => $tags[0] . '>',
            'closing' => '<' . $tags[1],
            default => throw new RuntimeException('Undefined snippet type: ' . $type),
        };
    }
}
