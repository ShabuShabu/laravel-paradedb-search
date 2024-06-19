<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\DisjunctionMax;
use ShabuShabu\ParadeDB\Query\Expressions\Regex;

it('returns documents that match one or more of the specified subqueries')
    ->expect(new DisjunctionMax([
        Builder::make()->where('description', 'shoes'),
        new Regex('category', '(hardcover|wireless)'),
        'color:IN [blue, green]',
    ]))
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.regex(field => 'category', pattern => '(hardcover|wireless)'), paradedb.parse(query_string => 'color:IN [blue, green]')], tie_breaker => NULL::real)");

it('returns documents that match one or more of the specified subqueries in a fluid manner')
    ->expect(
        DisjunctionMax::query()
            ->add(Builder::make()->where('description', 'shoes'))
            ->add(new Regex('category', '(hardcover|wireless)'))
            ->add('color:IN [blue, green]', when: false)
            ->tieBreaker(1.3)
    )
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.regex(field => 'category', pattern => '(hardcover|wireless)')], tie_breaker => 1.3)");

it('applies a tie breaker')
    ->expect(new DisjunctionMax([
        Builder::make()->where('description', 'shoes'),
        'color:IN [blue, green]',
    ], 2))
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.parse(query_string => 'color:IN [blue, green]')], tie_breaker => 2)");
