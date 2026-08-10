<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use ShabuShabu\ParadeDB\Expressions\v2\Support\Type;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use ShabuShabu\ParadeDB\Schema\Composite;
use ShabuShabu\ParadeDB\Schema\Index;
use ShabuShabu\ParadeDB\Schema\Schema;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades;

pest()->group('macros');

it('uses the bm25 blueprint macro correctly with an ', function (mixed $schema) {
    // @phpstan-ignore-next-line
    ($blueprint = new Blueprint(DB::connection(), 'testing'))->bm25($schema);

    expect($blueprint->toSql()[0])
        ->toBe(
            <<<'SQL'
            create index "testing_bm25_idx" on "testing" using bm25 ("id", ("name"::pdb.unicode_words('remove_emojis=true')), "created_at") with (key_field = id)
            SQL
        );
})->with([
    'array' => [[
        'id',
        new UnicodeWords('name')->removeEmojis(),
        'created_at',
    ]],
    'closure' => [fn () => function (Index $index) {
        $index->id();
        $index->unicodeWords('name')->removeEmojis();
        $index->column('created_at');
    }],
    'schema class' => [schemaClass(3)],
]);

it('uses the createCompositeType schema macro with an ', function (mixed $schema) {
    $result = Facades\Schema::createCompositeType('item_fields', $schema);

    // create type "item_fields" as ("name" pdb.literal, "description" text, "category" text)

    expect($result)->toBeTrue()
        ->and(DB::table('pg_type')->where('typname', 'item_fields')->exists())->toBeTrue();

    DB::statement('DROP TYPE "item_fields"');
})->with([
    'array' => [[
        new Literal('name'),
        'description text',
        new Type('category', 'text'),
    ]],
    'closure' => [fn () => function (Composite $schema) {
        $schema->literal('name');
        $schema->column('description', 'text');
        $schema->column('category', 'text');
    }],
]);

it('uses the search macro correctly', function () {
    Team::search()->get();
})->throwsNoExceptions();

function schemaClass(int $version): Schema
{
    return new readonly class($version) extends Schema
    {
        protected function v1(Index $index): void
        {
            $index->id();
            $index->unicodeWords('name');
        }

        protected function v2(Index $index): void
        {
            $index->unicodeWords('name')->removeEmojis();
        }

        protected function v3(Index $index): void
        {
            $index->column('created_at');
        }
    };
}
