<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\ConstScore;

it('applies a constant score to an expression query')
    ->expect(new ConstScore(new All(), 2))
    ->toBeExpression('paradedb.const_score(score => 2, query => paradedb.all())');

it('applies a constant score to a string query')
    ->expect(new ConstScore('description:shoes', 2))
    ->toBeExpression("paradedb.const_score(score => 2, query => paradedb.parse(query_string => 'description:shoes'))");

it('applies a constant score to a builder query')
    ->expect(new ConstScore(Builder::make()->where('description', 'shoes'), 2))
    ->toBeExpression("paradedb.const_score(score => 2, query => paradedb.parse(query_string => 'description:shoes'))");
