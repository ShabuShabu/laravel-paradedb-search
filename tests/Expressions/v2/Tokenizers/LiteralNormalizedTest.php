<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Stemmer;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\LiteralNormalized;

it('generates the correct literal normalized tokenizer')
    ->expect(new LiteralNormalized('description'))
    ->toBeExpression('description::pdb.literal_normalized');

it('generates the correct literal normalized tokenizer with filters')
    ->expect((new LiteralNormalized('description'))->stemmer(Stemmer::english))
    ->toBeExpression("description::pdb.literal_normalized('stemmer=english')");
