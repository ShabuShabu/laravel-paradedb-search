<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\All;

it('matches all documents')
    ->expect(new All())
    ->toBeExpression('paradedb.all()');
