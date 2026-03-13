<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Blank;

it('matches no documents')
    ->expect(new Blank)
    ->toBeExpression('paradedb.empty()');
