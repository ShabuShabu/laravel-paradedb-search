<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Tokenizers;

it('lists all tokenizers')
    ->expect(new Tokenizers)
    ->toBeExpression('paradedb.tokenizers()');
