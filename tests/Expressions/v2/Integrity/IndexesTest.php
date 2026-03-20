<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Integrity\Indexes;

pest()->group('v2', 'integrity');

it('gets all indexes')
    ->expect(new Indexes)
    ->toBeExpression('pdb.indexes()');
