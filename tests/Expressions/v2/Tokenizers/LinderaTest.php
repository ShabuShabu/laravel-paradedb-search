<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Dictionary;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Lindera;

pest()->group('v2', 'tokenizers');

it('generates the correct lindera tokenizer')
    ->expect(new Lindera('description', Dictionary::korean))
    ->toBeExpression('"description"::pdb.lindera(korean)');

it('sets the dictionary')
    ->expect((new Lindera('description', Dictionary::korean))->getParameters())
    ->toBe(['korean']);

it('generates the correct lindera tokenizer with filters')
    ->expect((new Lindera('description', Dictionary::japanese))->removeLong(3))
    ->toBeExpression("\"description\"::pdb.lindera(japanese, 'remove_long=3')");
