<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Tokenizer;

it('configures a tokenizer')
    ->expect(new Tokenizer('whitespace', lowercase: true))
    ->toBeExpression("paradedb.tokenizer(name => 'whitespace', lowercase => true)");
