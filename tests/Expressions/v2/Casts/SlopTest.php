<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Slop;
use Tpetry\QueryExpressions\Value\Value;

pest()->group('v2', 'casts');

it('adds slop to a column')
    ->expect(new Slop('shoes', 2))
    ->toBeExpression('"shoes"::pdb.slop(2)');

it('adds slop to a string')
    ->expect(new Slop(new Value('shoes'), 2))
    ->toBeExpression("'shoes'::pdb.slop(2)");

it('adds slop to a text array')
    ->expect(new Slop(['running', 'shoes'], 2))
    ->toBeExpression("ARRAY['running', 'shoes']::pdb.slop(2)");
