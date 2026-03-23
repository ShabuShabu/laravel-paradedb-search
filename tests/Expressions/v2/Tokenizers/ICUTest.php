<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\ICU;

pest()->group('v2', 'tokenizers');

it('generates the correct icu tokenizer')
    ->expect(new ICU('description'))
    ->toBeExpression('"description"::pdb.icu');

it('can be used as a type')
    ->expect((new ICU('description'))->useAsType())
    ->toBeExpression('"description" pdb.icu');

it('generates the correct icu tokenizer with filters')
    ->expect((new ICU('description'))->alphaNumOnly())
    ->toBeExpression("\"description\"::pdb.icu('alpha_num_only=true')");
