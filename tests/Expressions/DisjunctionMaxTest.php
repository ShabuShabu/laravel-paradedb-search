<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;
use ShabuShabu\ParadeDB\Expressions\Regex;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('returns documents that match one or more of the specified subqueries')
    ->expect(new DisjunctionMax([
        Query::string()->where('description', 'shoes'),
        new Regex('category', '(hardcover|wireless)'),
        'color:IN [blue, green]',
    ]))
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.regex(field => 'category', pattern => '(hardcover|wireless)'), paradedb.parse(query_string => 'color:IN [blue, green]')])");

it('returns documents that match one or more of the specified subqueries in a fluid manner')
    ->expect(
        DisjunctionMax::query()
            ->add(Query::string()->where('description', 'shoes'))
            ->add(new Regex('category', '(hardcover|wireless)'))
            ->add('color:IN [blue, green]', when: false)
            ->tieBreaker(1.3)
    )
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.regex(field => 'category', pattern => '(hardcover|wireless)')], tie_breaker => 1.3)");

it('applies a tie breaker')
    ->expect(new DisjunctionMax([
        Query::string()->where('description', 'shoes'),
        'color:IN [blue, green]',
    ], 2))
    ->toBeExpression("paradedb.disjunction_max(disjuncts => ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.parse(query_string => 'color:IN [blue, green]')], tie_breaker => 2)");
