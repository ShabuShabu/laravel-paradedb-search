<?php

declare(strict_types=1);

namespace ShabuShabu\ParadeDB\Expressions\Concerns;

use RuntimeException;

trait Taggable
{
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
