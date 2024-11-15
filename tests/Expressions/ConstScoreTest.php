<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\ConstScore;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('applies a constant score to an expression query')
    ->expect(new ConstScore(new All, 2))
    ->toBeExpression('paradedb.const_score(score => 2, query => paradedb.all())');

it('applies a constant score to a string query')
    ->expect(new ConstScore('description:shoes', 2))
    ->toBeExpression("paradedb.const_score(score => 2, query => paradedb.parse(query_string => 'description:shoes', lenient => NULL::boolean, conjunction_mode => NULL::boolean))");

it('applies a constant score to a builder query')
    ->expect(new ConstScore(Query::string()->where('description', 'shoes'), 2))
    ->toBeExpression("paradedb.const_score(score => 2, query => paradedb.parse(query_string => 'description:shoes', lenient => NULL::boolean, conjunction_mode => NULL::boolean))");
