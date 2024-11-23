<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Tokenizers;

it('lists all tokenizers')
    ->expect(new Tokenizers)
    ->toBeExpression('paradedb.tokenizers()');
