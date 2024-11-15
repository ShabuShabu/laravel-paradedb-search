<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression;

it('finds documents within a given range: ', function (\ShabuShabu\ParadeDB\Expressions\Ranges\RangeExpression $range, string $expression): void {
    expect(new Range('column', $range))->toBeExpression($expression);
})->with([
    'date range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-12', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10,2024-06-12)'::daterange)",
    ],
    'date range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10,2024-06-12]'::daterange)",
    ],
    'date range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-12', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10,2024-06-12]'::daterange)",
    ],
    'date range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10,2024-06-12)'::daterange)",
    ],
    'date range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date(null, '2024-06-12', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12)'::daterange)",
    ],
    'date range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10,)'::daterange)",
    ],
    'timestamp range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,2024-06-12 15:27:32]'::tsrange)",
    ],
    'timestamp range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32,2024-06-12 15:27:32]'::tsrange)",
    ],
    'timestamp range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp(null, '2024-06-12 15:27:32', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12 15:27:32)'::tsrange)",
    ],
    'timestamp range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp('2024-06-10 15:27:32', null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32,)'::tsrange)",
    ],
    'timestamptz range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45]'::tstzrange)",
    ],
    'timestamptz range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45]'::tstzrange)",
    ],
    'timestamptz range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2024-06-10 15:27:32+05:45,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz(null, '2024-06-12 15:27:32+05:45', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,2024-06-12 15:27:32+05:45)'::tstzrange)",
    ],
    'timestamptz range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz('2024-06-10 15:27:32+05:45', null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2024-06-10 15:27:32+05:45,)'::tstzrange)",
    ],
    'int4 range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,5)'::int4range)",
    ],
    'int4 range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2,5]'::int4range)",
    ],
    'int4 range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2,5]'::int4range)",
    ],
    'int4 range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2,5)'::int4range)",
    ],
    'int4 range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(null, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,5)'::int4range)",
    ],
    'int4 range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,)'::int4range)",
    ],
    'int8 range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,5)'::int8range)",
    ],
    'int8 range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[2,5]'::int8range)",
    ],
    'int8 range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(2,5]'::int8range)",
    ],
    'int8 range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(2,5)'::int8range)",
    ],
    'int8 range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(null, 5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,5)'::int8range)",
    ],
    'int8 range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[2,)'::int8range)",
    ],
    'numeric range include exclude' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, 3.5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[1.5,3.5)'::numrange)",
    ],
    'numeric range include all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, 3.5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => '[1.5,3.5]'::numrange)",
    ],
    'numeric range exclude include' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, 3.5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => '(1.5,3.5]'::numrange)",
    ],
    'numeric range exclude all' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, 3.5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => '(1.5,3.5)'::numrange)",
    ],
    'numeric range unbounded start' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(null, 3.5, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[,3.5)'::numrange)",
    ],
    'numeric range unbounded end' => [
        new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => '[1.5,)'::numrange)",
    ],
]);

it('panics for unbounded lower and upper values', function (string $class) {
    (new $class(null, null, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll))->getValue(grammar());
})->with([
    'date' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Date::class],
    'timestamp' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp::class],
    'timestamptz' => [\ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz::class],
    'int4' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Int4::class],
    'int8' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Int8::class],
    'numeric' => [\ShabuShabu\ParadeDB\Expressions\Ranges\Numeric::class],
])->throws(
    \ShabuShabu\ParadeDB\Expressions\Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);

it('panics for wrong lower and upper value order', function (RangeExpression $expression) {
    $expression->getValue(grammar());
})->with([
    'date' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Date('2024-06-10', '2024-06-09', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
    'timestamp' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-09 15:27:32', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
    'timestamptz' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-09 15:27:32+05:45', \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
    'int4' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Int4(2, 1, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
    'int8' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Int8(2, 1, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
    'numeric' => [new \ShabuShabu\ParadeDB\Expressions\Ranges\Numeric(1.5, 1.2, \ShabuShabu\ParadeDB\Expressions\Ranges\Bounds::excludeAll)],
])->throws(
    \ShabuShabu\ParadeDB\Expressions\Ranges\InvalidRange::class,
    'Range values must be in order from lowest to highest'
);
