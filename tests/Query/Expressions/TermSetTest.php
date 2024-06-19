<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\Ranges\Int8;
use ShabuShabu\ParadeDB\Query\Expressions\Term;
use ShabuShabu\ParadeDB\Query\Expressions\TermSet;

it('matches documents containing a specified term')
    ->expect(new TermSet([
        new Term('description', 'shoes'),
        new Term('rating', new Int8(2, 5)),
    ]))
    ->toBeExpression("paradedb.term_set(terms => ARRAY[paradedb.term(field => 'description', value => 'shoes'), paradedb.term(field => 'rating', value => '(2,5]'::int8range)])");

it('matches documents containing a specified term in a fliud manner')
    ->expect(
        TermSet::query()
            ->add(new Term('description', 'shoes'))
            ->add(new Term('rating', new Int8(2, 5)))
    )
    ->toBeExpression("paradedb.term_set(terms => ARRAY[paradedb.term(field => 'description', value => 'shoes'), paradedb.term(field => 'rating', value => '(2,5]'::int8range)])");

it('provides a default value')
    ->expect(new TermSet([]))
    ->toBeExpression('paradedb.term_set(terms => ARRAY[]::paradedb.searchqueryinput[])');

it('panics for non-terms in the set', function () {
    (new TermSet(['description:shoes']))->getValue(grammar());
})->throws(UnexpectedValueException::class);
