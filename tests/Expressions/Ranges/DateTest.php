<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Carbon;
use ShabuShabu\ParadeDB\Expressions\Ranges;

pest()->group('ranges', 'expressions');

it('generates a correct date range')
    ->expect(new Ranges\Date('2024-06-10', Carbon::parse('2024-06-12'), Ranges\Bounds::includeStartExcludeEnd))
    ->toBeExpression("'[2024-06-10,2024-06-12)'::daterange");

it('generates a correct date range with unbounded upper')
    ->expect(new Ranges\Date('2024-06-10', null, Ranges\Bounds::includeAll))
    ->toBeExpression("'[2024-06-10,]'::daterange");

it('generates a correct date range with unbounded lower')
    ->expect(new Ranges\Date(null, Carbon::parse('2024-06-12'), Ranges\Bounds::excludeStartIncludeEnd))
    ->toBeExpression("'(,2024-06-12]'::daterange");

it('panics for unbounded lower and upper values', function () {
    (new Ranges\Date(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);