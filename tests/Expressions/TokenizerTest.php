<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Tokenizer;

it('configures a tokenizer')
    ->expect(new Tokenizer('whitespace', lowercase: true))
    ->toBeExpression("paradedb.tokenizer(name => 'whitespace', lowercase => true)");
