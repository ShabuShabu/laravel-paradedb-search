<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Lindera;
use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Dictionary;

it('generates the correct lindera tokenizer')
    ->expect(new Lindera('description', Dictionary::korean))
    ->toBeExpression('description::pdb.lindera(korean)');

it('generates the correct lindera tokenizer with filters')
    ->expect((new Lindera('description', Dictionary::korean))->removeLong(3))
    ->toBeExpression("description::pdb.lindera(korean, 'remove_long=3')");
