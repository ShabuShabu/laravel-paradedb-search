<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Tokenize;
use ShabuShabu\ParadeDB\Expressions\Tokenizer;

it('tokenizes text')
    ->expect(new Tokenize(new Tokenizer('whitespace'), 'Just a test'))
    ->toBeExpression("paradedb.tokenize(paradedb.tokenizer(name => 'whitespace'), 'Just a test')");
