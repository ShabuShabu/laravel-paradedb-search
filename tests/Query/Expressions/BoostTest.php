<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\Boost;

it('boosts an expression query')
    ->expect(new Boost(new All(), 2))
    ->toBeExpression('paradedb.boost(boost => 2, query => paradedb.all())');

it('boosts a string query')
    ->expect(new Boost('description:shoes', 1.5))
    ->toBeExpression("paradedb.boost(boost => 1.5, query => paradedb.parse(query_string => 'description:shoes'))");

it('boosts a builder query')
    ->expect(new Boost(Builder::make()->where('description', 'shoes'), 2))
    ->toBeExpression("paradedb.boost(boost => 2, query => paradedb.parse(query_string => 'description:shoes'))");
