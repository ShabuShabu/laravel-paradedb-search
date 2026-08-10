<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use ShabuShabu\ParadeDB\Schema\Index;

pest()->group('schema');

it('collects index columns', function () {
    $index = new Index;

    $index->id();
    $index->column('name');
    $index->column('description');
    $tags = $index->literal('tags');
    $summary = $index->unicodeWords('summary')->removeEmojis();

    expect($tags)->toBeInstanceOf(Literal::class)
        ->and($summary)->toBeInstanceOf(UnicodeWords::class)
        ->and($summary->getConfig()[0])->toBe("'remove_emojis=true'")
        ->and($index->columns())->toBe([
            'id',
            'name',
            'description',
            $tags,
            $summary,
        ]);
});
