<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Carbon;
use ShabuShabu\ParadeDB\Expressions\Ranges;

pest()->group('ranges', 'expressions');

it('generates a correct timestamp range')
    ->expect(new Ranges\Timestamp('2024-06-10 15:27:32', Carbon::parse('2024-06-12 15:27:32'), Ranges\Bounds::includeStartExcludeEnd))
    ->toBeExpression("'[2024-06-10 15:27:32,2024-06-12 15:27:32)'::tsrange");

it('generates a correct timestamp range with unbounded upper')
    ->expect(new Ranges\Timestamp('2024-06-10 15:27:32', null, Ranges\Bounds::excludeAll))
    ->toBeExpression("'(2024-06-10 15:27:32,)'::tsrange");

it('generates a correct timestamp range with unbounded lower')
    ->expect(new Ranges\Timestamp(null, Carbon::parse('2024-06-12 15:27:32'), Ranges\Bounds::excludeStartIncludeEnd))
    ->toBeExpression("'(,2024-06-12 15:27:32]'::tsrange");

it('panics for unbounded lower and upper values', function () {
    (new Ranges\Timestamp(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);