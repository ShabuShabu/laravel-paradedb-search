<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;

pest()->group('macros');

it('uses the bm25 blueprint macro correctly', function () {
    // @phpstan-ignore-next-line
    ($blueprint = new Blueprint(DB::connection(), 'testing'))->bm25([
        'id',
        (new UnicodeWords('name'))->removeEmojis(),
        'created_at',
    ]);

    expect($blueprint->toSql()[0])
        ->toBe(
            <<<'SQL'
            create index "testing_bm25_idx" on "testing" using bm25 ("id", ("name"::pdb.unicode_words('remove_emojis=true')), "created_at") with (key_field = id)
            SQL
        );
});
