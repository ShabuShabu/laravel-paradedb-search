<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;

it('uses the bm25 blueprint macro correctly', function () {
    $blueprint = new Blueprint(DB::connection(), 'testing');
    // @phpstan-ignore-next-line
    $blueprint->bm25(
        name: 'testing_bm25_idx',
        columns: [
            'id',
            (new UnicodeWords('name'))->removeEmojis(),
            'description',
            'created_at',
        ],
    );

    expect($blueprint->toSql()[0])
        ->toBe(<<<SQL
            create index "testing_bm25_idx" on "testing" using bm25 ("id", ("name"::pdb.unicode_words('remove_emojis=true')), "description", "created_at") with (key_field = id)
            SQL
        );
});
