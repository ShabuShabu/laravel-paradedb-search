<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Types\Fuzzy;
use Tpetry\QueryExpressions\Value\Value;

pest()->group('v2', 'types');

it('assigns a constant score to a column')
    ->expect(new Fuzzy('description', 1))
    ->toBeExpression('"description"::pdb.fuzzy(1, f, f)');

it('assigns a constant score to a string')
    ->expect(new Fuzzy(new Value('description'), 1))
    ->toBeExpression("'description'::pdb.fuzzy(1, f, f)");

it('panics for a distance below 0', function () {
    (new Fuzzy('description', -1))->getValue(grammar());
})->throws(InvalidArgumentException::class, 'Edit distance must be between 0 and 2.');

it('panics for a distance above 2', function () {
    (new Fuzzy('description', 3))->getValue(grammar());
})->throws(InvalidArgumentException::class, 'Edit distance must be between 0 and 2.');
