<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\All;

it('matches all documents')
    ->expect(new All)
    ->toBeExpression('pdb.all()');