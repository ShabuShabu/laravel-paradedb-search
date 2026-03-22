<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int8;
use ShabuShabu\ParadeDB\Expressions\Ranges\InvalidRange;
use ShabuShabu\ParadeDB\Expressions\Ranges\Numeric;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;
use ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\v1\Range;

pest()->group('v1');

it('finds documents within a given range: ', function (RangeExpression $range, string $expression): void {
    expect(new Range('column', $range))->toBeExpression($expression);
})->with([
    'date range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-12', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10,2024-06-12)'::daterange)",
    ],
    'date range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10,2024-06-12]'::daterange)",
    ],
    'date range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-12', Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10,2024-06-12]'::daterange)",
    ],
    'date range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10,2024-06-12)'::daterange)",
    ],
    'date range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(null, '2024-06-12', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12)'::daterange)",
    ],
    'date range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10,)'::daterange)",
    ],
    'timestamp range include exclude' => [
        new Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range include all' => [
        new Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,2024-06-12 15:27:32]'::tsrange)",
    ],
    'timestamp range exclude include' => [
        new Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32,2024-06-12 15:27:32]'::tsrange)",
    ],
    'timestamp range exclude all' => [
        new Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range unbounded start' => [
        new Timestamp(null, '2024-06-12 15:27:32', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range unbounded end' => [
        new Timestamp('2024-06-10 15:27:32', null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,)'::tsrange)",
    ],
    'timestamptz range include exclude' => [
        new TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range include all' => [
        new TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45]'::tstzrange)",
    ],
    'timestamptz range exclude include' => [
        new TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45]'::tstzrange)",
    ],
    'timestamptz range exclude all' => [
        new TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range unbounded start' => [
        new TimestampTz(null, '2024-06-12 15:27:32+05:45', Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range unbounded end' => [
        new TimestampTz('2024-06-10 15:27:32+05:45', null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,)'::tstzrange)",
    ],
    'int4 range include exclude' => [
        new Int4(2, 5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,5)'::int4range)",
    ],
    'int4 range include all' => [
        new Int4(2, 5, Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2,5]'::int4range)",
    ],
    'int4 range exclude include' => [
        new Int4(2, 5, Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2,5]'::int4range)",
    ],
    'int4 range exclude all' => [
        new Int4(2, 5, Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2,5)'::int4range)",
    ],
    'int4 range unbounded start' => [
        new Int4(null, 5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,5)'::int4range)",
    ],
    'int4 range unbounded end' => [
        new Int4(2, null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,)'::int4range)",
    ],
    'int8 range include exclude' => [
        new Int8(2, 5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,5)'::int8range)",
    ],
    'int8 range include all' => [
        new Int8(2, 5, Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2,5]'::int8range)",
    ],
    'int8 range exclude include' => [
        new Int8(2, 5, Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2,5]'::int8range)",
    ],
    'int8 range exclude all' => [
        new Int8(2, 5, Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2,5)'::int8range)",
    ],
    'int8 range unbounded start' => [
        new Int8(null, 5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,5)'::int8range)",
    ],
    'int8 range unbounded end' => [
        new Int8(2, null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,)'::int8range)",
    ],
    'numeric range include exclude' => [
        new Numeric(1.5, 3.5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[1.5,3.5)'::numrange)",
    ],
    'numeric range include all' => [
        new Numeric(1.5, 3.5, Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[1.5,3.5]'::numrange)",
    ],
    'numeric range exclude include' => [
        new Numeric(1.5, 3.5, Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(1.5,3.5]'::numrange)",
    ],
    'numeric range exclude all' => [
        new Numeric(1.5, 3.5, Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(1.5,3.5)'::numrange)",
    ],
    'numeric range unbounded start' => [
        new Numeric(null, 3.5, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,3.5)'::numrange)",
    ],
    'numeric range unbounded end' => [
        new Numeric(1.5, null, Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[1.5,)'::numrange)",
    ],
]);

it('panics for unbounded lower and upper values', function (string $class) {
    (new $class(null, null, Bounds::excludeAll))->getValue(grammar());
})->with([
    'date' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Date::class],
    'timestamp' => [Timestamp::class],
    'timestamptz' => [TimestampTz::class],
    'int4' => [Int4::class],
    'int8' => [Int8::class],
    'numeric' => [Numeric::class],
])->throws(
    InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);

it('panics for wrong lower and upper value order', function (RangeExpression $expression) {
    $expression->getValue(grammar());
})->with([
    'date' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-09', Bounds::excludeAll)],
    'timestamp' => [new Timestamp('2024-06-10 15:27:32', '2024-06-09 15:27:32', Bounds::excludeAll)],
    'timestamptz' => [new TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-09 15:27:32+05:45', Bounds::excludeAll)],
    'int4' => [new Int4(2, 1, Bounds::excludeAll)],
    'int8' => [new Int8(2, 1, Bounds::excludeAll)],
    'numeric' => [new Numeric(1.5, 1.2, Bounds::excludeAll)],
])->throws(
    InvalidRange::class,
    'Range values must be in order from lowest to highest'
);
