<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Ranges;

pest()->group('ranges', 'expressions');

it('generates a correct int4 range')
    ->expect(new Ranges\Int4(2, 5, Ranges\Bounds::includeStartExcludeEnd))
    ->toBeExpression("'[2,5)'::int4range");

it('generates a correct int4 range with unbounded upper')
    ->expect(new Ranges\Int4(2, null, Ranges\Bounds::includeAll))
    ->toBeExpression("'[2,]'::int4range");

it('generates a correct int4 range with unbounded lower')
    ->expect(new Ranges\Int4(null, 5, Ranges\Bounds::excludeAll))
    ->toBeExpression("'(,5)'::int4range");

it('panics for unbounded lower and upper values', function () {
    (new Ranges\Int4(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);
