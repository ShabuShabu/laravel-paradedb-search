<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\Distance;
use ShabuShabu\ParadeDB\Query\Expressions\Similarity;

it('parses a similarity query')
    ->expect(new Similarity('embedding', Distance::l2, [1, 2, 3], false))
    ->toBeExpression("embedding <-> '[1,2,3]'");

it('parses and escapes a similarity query')
    ->expect(new Similarity('embedding', Distance::l2, [1, 2, 3]))
    ->toBeExpression("embedding <-> ''[1,2,3]''");
