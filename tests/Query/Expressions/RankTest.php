<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Query\Expression;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;

it('ranks a query')
    ->expect(new Rank('id'))
    ->toBeExpression('paradedb.rank_bm25(key => "id", alias => NULL::text)');

it('ranks a query from an expression')
    ->expect(new Rank(new Expression('id')))
    ->toBeExpression('paradedb.rank_bm25(key => id, alias => NULL::text)');

it('ranks a query using an alias')
    ->expect(new Rank('id', 'id_rank'))
    ->toBeExpression(<<<'EXP'
        paradedb.rank_bm25(key => "id", alias => 'id_rank')
        EXP
    );
