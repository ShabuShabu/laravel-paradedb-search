<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Relation;
use ShabuShabu\ParadeDB\Expressions\v2\RangeTerm;

pest()->group('v2', 'expressions');

it('finds ranges for a given value')
    ->expect(new RangeTerm(1))
    ->toBeExpression('pdb.range_term(term => 1)');

it('compares ranges to a given range')
    ->expect(new RangeTerm(new Int4(10, 12), Relation::intersects))
    ->toBeExpression("pdb.range_term(term => '(10,12]'::int4range, relation => 'Intersects')");

it('panics for a range value without a relation', function () {
    (new RangeTerm(new Int4(10, 12)))->getValue(grammar());
})->throws(RuntimeException::class);
