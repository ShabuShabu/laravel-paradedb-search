<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\Whitespace;

it('generates the correct whitespace tokenizer')
    ->expect(new Whitespace('description'))
    ->toBeExpression('"description"::pdb.whitespace');
