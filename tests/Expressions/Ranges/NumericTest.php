<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Ranges;

pest()->group('ranges', 'expressions');

it('generates a correct int8 range')
    ->expect(new Ranges\Numeric(1.5, 3.5, Ranges\Bounds::includeStartExcludeEnd))
    ->toBeExpression("'[1.5,3.5)'::numrange");

it('generates a correct int8 range with unbounded upper')
    ->expect(new Ranges\Numeric(1.5, null, Ranges\Bounds::includeAll))
    ->toBeExpression("'[1.5,]'::numrange");

it('generates a correct int8 range with unbounded lower')
    ->expect(new Ranges\Numeric(null, 3.5, Ranges\Bounds::excludeAll))
    ->toBeExpression("'(,3.5)'::numrange");

it('panics for unbounded lower and upper values', function () {
    (new Ranges\Numeric(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);
