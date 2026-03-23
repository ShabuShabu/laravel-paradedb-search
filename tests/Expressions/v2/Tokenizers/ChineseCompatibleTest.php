<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Tokenizers\ChineseCompatible;

pest()->group('v2', 'tokenizers');

it('generates the correct chinese compatible tokenizer')
    ->expect(new ChineseCompatible('description'))
    ->toBeExpression('"description"::pdb.chinese_compatible');

it('can be used as a type')
    ->expect((new ChineseCompatible('description'))->useAsType())
    ->toBeExpression('"description" pdb.chinese_compatible');

it('generates the correct chinese compatible tokenizer with filters')
    ->expect((new ChineseCompatible('description'))->asciiFolding())
    ->toBeExpression("\"description\"::pdb.chinese_compatible('ascii_folding=true')");
