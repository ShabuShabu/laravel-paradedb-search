<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use ShabuShabu\ParadeDB\Query\Expressions\Range;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\RangeExpression;

it('finds documents within a given range: ', function (Ranges\RangeExpression $range, string $expression): void {
    expect(new Range('column', $range))->toBeExpression($expression);
})->with([
    'date range include exclude' => [
        new Ranges\Date('2024-06-10', '2024-06-12', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10','2024-06-12')::daterange)",
    ],
    'date range include all' => [
        new Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => ['2024-06-10','2024-06-12']::daterange)",
    ],
    'date range exclude include' => [
        new Ranges\Date('2024-06-10', '2024-06-12', Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => ('2024-06-10','2024-06-12']::daterange)",
    ],
    'date range exclude all' => [
        new Ranges\Date(Date::parse('2024-06-10'), Date::parse('2024-06-12'), Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => ('2024-06-10','2024-06-12')::daterange)",
    ],
    'date range unbounded start' => [
        new Ranges\Date(null, '2024-06-12', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,'2024-06-12')::daterange)",
    ],
    'date range unbounded end' => [
        new Ranges\Date('2024-06-10', null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10',)::daterange)",
    ],
    'timestamp range include exclude' => [
        new Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32','2024-06-12 15:27:32')::tsrange)",
    ],
    'timestamp range include all' => [
        new Ranges\Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32','2024-06-12 15:27:32']::tsrange)",
    ],
    'timestamp range exclude include' => [
        new Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-12 15:27:32', Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => ('2024-06-10 15:27:32','2024-06-12 15:27:32']::tsrange)",
    ],
    'timestamp range exclude all' => [
        new Ranges\Timestamp(Date::parse('2024-06-10 15:27:32'), Date::parse('2024-06-12 15:27:32'), Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => ('2024-06-10 15:27:32','2024-06-12 15:27:32')::tsrange)",
    ],
    'timestamp range unbounded start' => [
        new Ranges\Timestamp(null, '2024-06-12 15:27:32', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,'2024-06-12 15:27:32')::tsrange)",
    ],
    'timestamp range unbounded end' => [
        new Ranges\Timestamp('2024-06-10 15:27:32', null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32',)::tsrange)",
    ],
    'timestamptz range include exclude' => [
        new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32+05:45','2024-06-12 15:27:32+05:45')::tstzrange)",
    ],
    'timestamptz range include all' => [
        new Ranges\TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32+05:45','2024-06-12 15:27:32+05:45']::tstzrange)",
    ],
    'timestamptz range exclude include' => [
        new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-12 15:27:32+05:45', Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => ('2024-06-10 15:27:32+05:45','2024-06-12 15:27:32+05:45']::tstzrange)",
    ],
    'timestamptz range exclude all' => [
        new Ranges\TimestampTz(Date::parse('2024-06-10 15:27:32+05:45'), Date::parse('2024-06-12 15:27:32+05:45'), Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => ('2024-06-10 15:27:32+05:45','2024-06-12 15:27:32+05:45')::tstzrange)",
    ],
    'timestamptz range unbounded start' => [
        new Ranges\TimestampTz(null, '2024-06-12 15:27:32+05:45', Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,'2024-06-12 15:27:32+05:45')::tstzrange)",
    ],
    'timestamptz range unbounded end' => [
        new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => ['2024-06-10 15:27:32+05:45',)::tstzrange)",
    ],
    'int4 range include exclude' => [
        new Ranges\Int4(2, 5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [2,5)::int4range)",
    ],
    'int4 range include all' => [
        new Ranges\Int4(2, 5, Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => [2,5]::int4range)",
    ],
    'int4 range exclude include' => [
        new Ranges\Int4(2, 5, Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => (2,5]::int4range)",
    ],
    'int4 range exclude all' => [
        new Ranges\Int4(2, 5, Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => (2,5)::int4range)",
    ],
    'int4 range unbounded start' => [
        new Ranges\Int4(null, 5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,5)::int4range)",
    ],
    'int4 range unbounded end' => [
        new Ranges\Int4(2, null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [2,)::int4range)",
    ],
    'int8 range include exclude' => [
        new Ranges\Int8(2, 5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [2,5)::int8range)",
    ],
    'int8 range include all' => [
        new Ranges\Int8(2, 5, Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => [2,5]::int8range)",
    ],
    'int8 range exclude include' => [
        new Ranges\Int8(2, 5, Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => (2,5]::int8range)",
    ],
    'int8 range exclude all' => [
        new Ranges\Int8(2, 5, Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => (2,5)::int8range)",
    ],
    'int8 range unbounded start' => [
        new Ranges\Int8(null, 5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,5)::int8range)",
    ],
    'int8 range unbounded end' => [
        new Ranges\Int8(2, null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [2,)::int8range)",
    ],
    'numeric range include exclude' => [
        new Ranges\Numeric(1.5, 3.5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [1.5,3.5)::numrange)",
    ],
    'numeric range include all' => [
        new Ranges\Numeric(1.5, 3.5, Ranges\Bounds::includeAll),
        "paradedb.range(field => 'column', range => [1.5,3.5]::numrange)",
    ],
    'numeric range exclude include' => [
        new Ranges\Numeric(1.5, 3.5, Ranges\Bounds::excludeStartIncludeEnd),
        "paradedb.range(field => 'column', range => (1.5,3.5]::numrange)",
    ],
    'numeric range exclude all' => [
        new Ranges\Numeric(1.5, 3.5, Ranges\Bounds::excludeAll),
        "paradedb.range(field => 'column', range => (1.5,3.5)::numrange)",
    ],
    'numeric range unbounded start' => [
        new Ranges\Numeric(null, 3.5, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [,3.5)::numrange)",
    ],
    'numeric range unbounded end' => [
        new Ranges\Numeric(1.5, null, Ranges\Bounds::includeStartExcludeEnd),
        "paradedb.range(field => 'column', range => [1.5,)::numrange)",
    ],
]);

it('panics for unbounded lower and upper values', function (string $class) {
    (new $class(null, null, Ranges\Bounds::excludeAll))->getValue(grammar());
})->with([
    'date' => [Ranges\Date::class],
    'timestamp' => [Ranges\Timestamp::class],
    'timestamptz' => [Ranges\TimestampTz::class],
    'int4' => [Ranges\Int4::class],
    'int8' => [Ranges\Int8::class],
    'numeric' => [Ranges\Numeric::class],
])->throws(
    Ranges\InvalidRange::class,
    'Both upper and lower values cannot not be unbounded at the same time'
);

it('panics for wrong lower and upper value order', function (RangeExpression $expression) {
    $expression->getValue(grammar());
})->with([
    'date' => [new Ranges\Date('2024-06-10', '2024-06-09', Ranges\Bounds::excludeAll)],
    'timestamp' => [new Ranges\Timestamp('2024-06-10 15:27:32', '2024-06-09 15:27:32', Ranges\Bounds::excludeAll)],
    'timestamptz' => [new Ranges\TimestampTz('2024-06-10 15:27:32+05:45', '2024-06-09 15:27:32+05:45', Ranges\Bounds::excludeAll)],
    'int4' => [new Ranges\Int4(2, 1, Ranges\Bounds::excludeAll)],
    'int8' => [new Ranges\Int8(2, 1, Ranges\Bounds::excludeAll)],
    'numeric' => [new Ranges\Numeric(1.5, 1.2, Ranges\Bounds::excludeAll)],
])->throws(
    Ranges\InvalidRange::class,
    'Range values must be in order from lowest to highest'
);
