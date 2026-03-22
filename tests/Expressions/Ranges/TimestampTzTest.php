<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Carbon;
use ShabuShabu\ParadeDB\Expressions\Ranges;

pest()->group('ranges', 'expressions');

it('generates a correct timestamptz range')
    ->expect(new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', Carbon::parse('2024-06-12 15:27:32+05:45'), Ranges\Bounds::includeStartExcludeEnd))
    ->toBeExpression("'[2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45)'::tstzrange");

it('generates a correct timestamptz range with unbounded upper')
    ->expect(new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', null, Ranges\Bounds::excludeAll))
    ->toBeExpression("'(2024-06-10 15:27:32+05:45,)'::tstzrange");

it('generates a correct timestamptz range with unbounded lower')
    ->expect(new Ranges\TimestampTz(null, Carbon::parse('2024-06-12 15:27:32+05:45'), Ranges\Bounds::excludeStartIncludeEnd))
    ->toBeExpression("'(,2024-06-12 15:27:32+05:45]'::tstzrange");

it('panics for unbounded lower and upper values', function () {
    (new Ranges\TimestampTz(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);
