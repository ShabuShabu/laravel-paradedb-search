<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Support\Type;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use ShabuShabu\ParadeDB\Schema\Composite;

pest()->group('schema');

it('collects composite types', function () {
    $type = new Composite;

    $name = $type->column('name', 'text');
    $tags = $type->literal('tags');
    $summary = $type->unicodeWords('summary')->removeEmojis();

    expect($name)->toBeInstanceOf(Type::class)
        ->and($tags)->toBeInstanceOf(Literal::class)
        ->and($summary)->toBeInstanceOf(UnicodeWords::class)
        ->and($summary->getConfig()[0])->toBe("'remove_emojis=true'")
        ->and($type->columns())->toBe([
            $name,
            $tags,
            $summary,
        ]);
});
