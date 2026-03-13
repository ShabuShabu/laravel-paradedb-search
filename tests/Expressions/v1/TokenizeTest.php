<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Tokenize;
use ShabuShabu\ParadeDB\Expressions\v1\Tokenizer;

it('tokenizes text')
    ->expect(new Tokenize(new Tokenizer('whitespace'), 'Just a test'))
    ->toBeExpression("paradedb.tokenize(paradedb.tokenizer(name => 'whitespace'), 'Just a test')");
