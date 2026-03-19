<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers\UnicodeWords;

it('generates the correct unicode words tokenizer')
    ->expect(new UnicodeWords('description'))
    ->toBeExpression('"description"::pdb.unicode_words');

it('generates the correct unicode words tokenizer with filters')
    ->expect((new UnicodeWords('description'))->removeEmojis())
    ->toBeExpression("\"description\"::pdb.unicode_words('remove_emojis=true')");
