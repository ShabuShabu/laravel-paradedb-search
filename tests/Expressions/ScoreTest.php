<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Score;

it('ranks a query with default key')
    ->expect(new Score)
    ->toBeExpression('paradedb.score("id")');

it('ranks a query with a custom key')
    ->expect(new Score('test'))
    ->toBeExpression('paradedb.score("test")');
