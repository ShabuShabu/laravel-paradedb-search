<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Constant;
use Tpetry\QueryExpressions\Value\Value;

pest()->group('v2', 'casts');

it('assigns a constant score to a column')
    ->expect(new Constant('description', 2))
    ->toBeExpression('"description"::pdb.const(2)');

it('assigns a constant score to a string')
    ->expect(new Constant(new Value('shoes'), 2))
    ->toBeExpression("'shoes'::pdb.const(2)");
