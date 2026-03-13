<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\SourceCode;

it('generates the correct source code tokenizer')
    ->expect(new SourceCode('description'))
    ->toBeExpression('description::pdb.source_code');
