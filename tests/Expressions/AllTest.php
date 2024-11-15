<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\All;

it('matches all documents')
    ->expect(new All)
    ->toBeExpression('paradedb.all()');
