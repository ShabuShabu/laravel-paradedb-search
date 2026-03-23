<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Whitespace;

pest()->group('v2', 'tokenizers');

it('generates the correct whitespace tokenizer')
    ->expect(new Whitespace('description'))
    ->toBeExpression('"description"::pdb.whitespace');

it('can be used as a type')
    ->expect((new Whitespace('description'))->useAsType())
    ->toBeExpression('"description" pdb.whitespace');

it('generates the correct whitespace tokenizer with columnar storage')
    ->expect((new Whitespace('description'))->columnar())
    ->toBeExpression("\"description\"::pdb.whitespace('columnar=true')");
