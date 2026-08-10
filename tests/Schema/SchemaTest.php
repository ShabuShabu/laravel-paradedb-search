<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Literal;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\UnicodeWords;
use ShabuShabu\ParadeDB\Schema\Index;
use ShabuShabu\ParadeDB\Schema\Schema;

pest()->group('schema');

it('combines various versions into one definition', function () {
    $schema = fn (int $version) => new readonly class($version) extends Schema
    {
        protected function v1(Index $index): void
        {
            $index->id();
            $index->unicodeWords('name');
            $index->column('created_at');
        }

        protected function v2(Index $index): void
        {
            $index->unicodeWords('name')->removeEmojis();
            $index->remove('created_at');
        }

        protected function v3(Index $index): void
        {
            $index->literal('tags');
        }
    };

    $v1 = $schema(1)->toArray();

    expect($v1[0])->toBe('id')
        ->and($v1[2])->toBe('created_at')
        ->and($v1[1])->toBeInstanceOf(UnicodeWords::class)
        ->and($v1[1]->getConfig())->toBeEmpty();

    $v2 = $schema(2)->toArray();

    expect($v2[0])->toBe('id')
        ->and($v2[1])->toBeInstanceOf(UnicodeWords::class)
        ->and($v2[1]->getConfig()[0])->toBe("'remove_emojis=true'");

    $v3 = $schema(3)->toArray();

    expect($v3[0])->toBe('id')
        ->and($v3[1])->toBeInstanceOf(UnicodeWords::class)
        ->and($v3[1]->getConfig()[0])->toBe("'remove_emojis=true'")
        ->and($v3[2])->toBeInstanceOf(Literal::class);
});
