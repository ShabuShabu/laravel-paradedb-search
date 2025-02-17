<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Query\Expression;
use ShabuShabu\ParadeDB\Expressions\Similarity;
use ShabuShabu\ParadeDB\Operators\Distance;

it('parses a similarity query')
    ->expect(new Similarity('embedding', Distance::l2, [1, 2, 3]))
    ->toBeExpression("\"embedding\" <-> '[1,2,3]'");

it('parses a similarity expression query')
    ->expect(new Similarity(new Expression('embedding'), Distance::l2, [1, 2, 3]))
    ->toBeExpression("embedding <-> '[1,2,3]'");

it('parses a similarity query with string values')
    ->expect(new Similarity('embedding', Distance::l2, '[1,2,3]'))
    ->toBeExpression("\"embedding\" <-> '[1,2,3]'");

it('panics for an associative array', function () {
    (new Similarity('embedding', Distance::l2, ['1st' => 1, '2nd' => 2, '3rd' => 3]))->getValue(grammar());
})->throws(
    InvalidArgumentException::class,
    'Expected similarity values to be a list'
);
