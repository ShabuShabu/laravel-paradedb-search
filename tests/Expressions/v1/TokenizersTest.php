<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Tokenizers;

pest()->group('v1');

it('lists all tokenizers')
    ->expect(new Tokenizers)
    ->toBeExpression('paradedb.tokenizers()');
