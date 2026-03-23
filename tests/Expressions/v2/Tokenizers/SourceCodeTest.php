<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\SourceCode;

pest()->group('v2', 'tokenizers');

it('generates the correct source code tokenizer')
    ->expect(new SourceCode('description'))
    ->toBeExpression('"description"::pdb.source_code');

it('can be used as a type')
    ->expect((new SourceCode('description'))->useAsType())
    ->toBeExpression('"description" pdb.source_code');
