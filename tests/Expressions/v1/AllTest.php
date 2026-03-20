<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\All;

pest()->group('v1');

it('matches all documents')
    ->expect(new All)
    ->toBeExpression('paradedb.all()');
