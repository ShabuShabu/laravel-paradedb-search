<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Rank;
use ShabuShabu\ParadeDB\Expressions\v2\Similarity;
use ShabuShabu\ParadeDB\Operators\Distance;

pest()->group('v1');

it('ranks a simple query')
    ->expect(new Rank(['description', 'asc']))
    ->toBeExpression('RANK () OVER (ORDER BY "description" ASC)');

it('ranks an expression query')
    ->expect(new Rank([new Similarity('embedding', Distance::l2, [1, 2, 3]), 'asc']))
    ->toBeExpression('RANK () OVER (ORDER BY "embedding" <-> \'[1,2,3]\' ASC)');
