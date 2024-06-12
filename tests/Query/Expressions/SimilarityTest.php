<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Query\Expression;
use ShabuShabu\ParadeDB\Query\Expressions\Distance;
use ShabuShabu\ParadeDB\Query\Expressions\Similarity;

it('parses a similarity query')
    ->expect(new Similarity('embedding', Distance::l2, [1, 2, 3]))
    ->toBeExpression("\"embedding\" <-> '[1,2,3]'");

it('parses a similarity expression query')
    ->expect(new Similarity(new Expression('embedding'), Distance::l2, [1, 2, 3]))
    ->toBeExpression("embedding <-> '[1,2,3]'");

it('parses a similarity query with string values')
    ->expect(new Similarity('embedding', Distance::l2, '[1,2,3]'))
    ->toBeExpression("\"embedding\" <-> '[1,2,3]'");
