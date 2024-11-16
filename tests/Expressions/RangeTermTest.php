<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Relation;
use ShabuShabu\ParadeDB\Expressions\RangeTerm;

it('finds ranges for a given value')
    ->expect(new RangeTerm('weight_range', 1))
    ->toBeExpression("paradedb.range_term(field => 'weight_range', term => 1)");

it('compares ranges to a given range')
    ->expect(new RangeTerm('weight_range', new Int4(10, 12), Relation::intersects))
    ->toBeExpression("paradedb.range_term(field => 'weight_range', term => '(10,12]'::int4range, relation => 'Intersects')");

it('panics for a range value without a relation', function () {
    (new RangeTerm('weight_range', new Int4(10, 12)))->getValue(grammar());
})->throws(RuntimeException::class);
