<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\TantivyQL\InvalidFilter;
use ShabuShabu\ParadeDB\TantivyQL\Operators\Filter;
use ShabuShabu\ParadeDB\TantivyQL\Operators\Range;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('compiles a regular query', function () {
    $query = Query::string()->where('description', 'keyboard')->get();

    expect($query)->toBe('description:keyboard');
});

it('boosts a regular query', function () {
    $query = Query::string()->where('description', 'keyboard', 1)->get();

    expect($query)->toBe('description:keyboard^1');
});

it('quotes phrases in queries', function () {
    $query = Query::string()->where('description', 'black keyboard')->get();

    expect($query)->toBe('description:"black keyboard"');
});

it('boosts phrases in queries', function () {
    $query = Query::string()->where('description', 'black keyboard', 2)->get();

    expect($query)->toBe('description:"black keyboard"^2');
});

it('compiles a set query', function () {
    $query = Query::string()->where('description', ['keyboard', 'toy'])->get();

    expect($query)->toBe('description:IN [keyboard, toy]');
});

it('quotes values in a set query', function () {
    $query = Query::string()->where('description', ['black keyboard', 'toy'])->get();

    expect($query)->toBe('description:IN ["black keyboard", toy]');
});

it('compiles a slop query', function () {
    $query = Query::string()->where('description', 'black keyboard', slop: 1)->get();

    expect($query)->toBe('description:"black keyboard"~1');
});

it('compiles a boosted slop query', function () {
    $query = Query::string()->where('description', 'black keyboard', boost: 1, slop: 1)->get();

    expect($query)->toBe('description:"black keyboard"~1^1');
});

it('ignores the slop operator for single-word values', function () {
    $query = Query::string()->where('description', 'keyboard', slop: 1)->get();

    expect($query)->toBe('description:keyboard');
});

it('compiles a AND NOT query', function () {
    $query = Query::string()
        ->where('category', 'electronics')
        ->whereNot('description', 'keyboard')
        ->get();

    expect($query)->toBe('category:electronics AND NOT description:keyboard');
});

it('compiles an OR NOT query', function () {
    $query = Query::string()
        ->where('category', 'electronics')
        ->orWhereNot('description', 'keyboard')
        ->get();

    expect($query)->toBe('category:electronics OR NOT description:keyboard');
});

it('compiles a query from a closure', function () {
    $query = Query::string()->where(
        fn (Query $builder) => $builder
            ->where('category', 'electronics')
            ->orWhere('tag', 'office')
    )->get();

    expect($query)->toBe('(category:electronics OR tag:office)');
});

it('concatenates multiple conditions', function () {
    $query = Query::string()
        ->where('description', ['keyboard', 'toy'])
        ->where(
            fn (Query $builder) => $builder
                ->where('category', 'electronics')
                ->orWhere('tag', 'office')
        )
        ->get();

    expect($query)->toBe('description:IN [keyboard, toy] AND (category:electronics OR tag:office)');
});

it('resolves multiple nested closure queries', function () {
    $query = Query::string()
        ->where(
            fn (Query $builder) => $builder
                ->where('category', 'electronics')
                ->orWhere(
                    fn (Query $builder) => $builder
                        ->where('tag', 'office')
                        ->where('color', 'blue')
                )
        )
        ->get();

    expect($query)->toBe('(category:electronics OR (tag:office AND color:blue))');
});

it('escapes special characters in values: ', function (string $char) {
    $query = Query::string()->where('description', "Just some $char dummy text")->get();

    expect($query)->toBe(sprintf('description:"Just some \%s dummy text"', $char));
})->with([
    '\\' => ['\\'],
    '+' => ['+'],
    '^' => ['^'],
    '`' => ['`'],
    ':' => [':'],
    '{' => ['{'],
    '}' => ['}'],
    '"' => ['"'],
    '[' => ['['],
    ']' => [']'],
    '(' => ['('],
    ')' => [')'],
    '~' => ['~'],
    '!' => ['!'],
    '*' => ['*'],
]);

it('escapes multiple special characters', function () {
    $query = Query::string()->where(
        'description',
        'It (what) is worth ~200 euros!'
    )->get();

    expect($query)->toBe('description:"It \(what\) is worth \~200 euros\!"');
});

it('compiles an equality filter', function () {
    $query = Query::string()
        ->whereFilter('rating', Filter::equals, 4)
        ->get();

    expect($query)->toBe('rating:4');
});

it('compiles a simple range filter', function () {
    $query = Query::string()
        ->whereFilter('rating', '>', 4)
        ->get();

    expect($query)->toBe('rating:>4');
});

it('compiles a boolean filter', function () {
    $query = Query::string()
        ->whereFilter('is_available', '=', false)
        ->get();

    expect($query)->toBe('is_available:false');
});

it('compiles an inclusive range filter', function () {
    $query = Query::string()
        ->whereFIlter('rating', Range::includeAll, [2, 5])
        ->get();

    expect($query)->toBe('rating:[2 TO 5]');
});

it('compiles an exclusive range filter', function () {
    $query = Query::string()
        ->whereFilter('rating', Range::excludeAll, [2, 5])
        ->get();

    expect($query)->toBe('rating:{2 TO 5}');
});

it('compiles an OR filter', function () {
    $query = Query::string()
        ->where('description', 'keyboard')
        ->orWhereFilter('is_available', '=', false)
        ->get();

    expect($query)->toBe('description:keyboard OR is_available:false');
});

it('compiles an inclusive OR filter', function () {
    $query = Query::string()
        ->where('description', 'keyboard')
        ->orWhereFilter('rating', Range::includeAll, [2, 5])
        ->get();

    expect($query)->toBe('description:keyboard OR rating:[2 TO 5]');
});

it('compiles an exclusive OR filter', function () {
    $query = Query::string()
        ->where('description', 'keyboard')
        ->orWhereFilter('rating', Range::excludeAll, [2, 5])
        ->get();

    expect($query)->toBe('description:keyboard OR rating:{2 TO 5}');
});

it('panics for an unknown range operator', function () {
    Query::string()->whereFilter('rating', '()', [2, 4]);
})->throws(
    InvalidFilter::class,
    'Operator `()` is not a valid range operator. Valid operators are `[}`, `[]`, `{]` and `{}`',
);

it('panics for an unknown filter operator', function () {
    Query::string()->whereFilter('rating', '~', 3);
})->throws(
    InvalidFilter::class,
    'Operator `~` is not a valid filter operator. Valid operators are `=`, `<`, `<=`, `>` and `>=`',
);

it('panics for a range filter consisting of more than two values', function () {
    Query::string()->whereFilter('rating', Range::includeAll, [2, 4, 6]);
})->throws(
    InvalidFilter::class,
    'A range filter must be an array of exactly two values',
);

it('panics for a non-integer range filter', function () {
    Query::string()->whereFilter('rating', '[]', ['one', 'four']);
})->throws(
    InvalidFilter::class,
    'A range filter must consist only of integers',
);

it('panics for a range filter in the wrong order', function () {
    Query::string()->whereFilter('rating', '[]', [4, 2]);
})->throws(
    InvalidFilter::class,
    'Range filter values must be in order from lowest to highest',
);
