<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Rank;
use ShabuShabu\ParadeDB\Expressions\Similarity;
use ShabuShabu\ParadeDB\Operators\Distance;

it('ranks a simple query')
    ->expect(new Rank(['description', 'asc']))
    ->toBeExpression('RANK () OVER (ORDER BY "description" ASC)');

it('ranks an expression query')
    ->expect(new Rank([new Similarity('embedding', Distance::l2, [1, 2, 3]), 'asc']))
    ->toBeExpression('RANK () OVER (ORDER BY "embedding" <-> \'[1,2,3]\' ASC)');
