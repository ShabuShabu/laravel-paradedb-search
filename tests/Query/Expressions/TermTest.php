<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\Ranges\Int8;
use ShabuShabu\ParadeDB\Query\Expressions\Term;

it('matches documents containing a specified term')
    ->expect(new Term('description', 'shoes'))
    ->toBeExpression("paradedb.term(field => 'description', value => 'shoes')");

it('matches documents containing a specified expression')
    ->expect(new Term('rating', new Int8(2, 5)))
    ->toBeExpression("paradedb.term(field => 'rating', value => (2,5]::int8range)");
