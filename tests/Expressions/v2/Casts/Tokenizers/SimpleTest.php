<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers\Simple;

it('generates the correct simple tokenizer')
    ->expect(new Simple('description'))
    ->toBeExpression('"description"::pdb.simple');

it('generates the correct ngram tokenizer with filters')
    ->expect((new Simple('description'))->removeShort(2))
    ->toBeExpression("\"description\"::pdb.simple('remove_short=2')");
