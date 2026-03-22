<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Score;

pest()->group('v2', 'expressions');

it('ranks a query with default key')
    ->expect(new Score)
    ->toBeExpression('pdb.score("id")');

it('ranks a query with a custom key')
    ->expect(new Score('test'))
    ->toBeExpression('pdb.score("test")');
