<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers\LiteralNormalized;
use ShabuShabu\ParadeDB\Expressions\v2\Casts\Tokenizers\Stemmer;

it('generates the correct literal normalized tokenizer')
    ->expect(new LiteralNormalized('description'))
    ->toBeExpression('"description"::pdb.literal_normalized');

it('generates the correct literal normalized tokenizer with filters')
    ->expect((new LiteralNormalized('description'))->stemmer(Stemmer::english))
    ->toBeExpression("\"description\"::pdb.literal_normalized('stemmer=english')");
