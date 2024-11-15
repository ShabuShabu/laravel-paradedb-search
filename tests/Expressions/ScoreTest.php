<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Query\Expression;
use ShabuShabu\ParadeDB\Expressions\Score;

it('ranks a query')
    ->expect(new Score('id'))
    ->toBeExpression('paradedb.score("id")');

it('ranks a query from an expression')
    ->expect(new Score(new Expression('id')))
    ->toBeExpression('paradedb.score(id)');
