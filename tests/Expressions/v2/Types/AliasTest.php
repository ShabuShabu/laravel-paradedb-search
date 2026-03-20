<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Types\Alias;
use Tpetry\QueryExpressions\Function\String\Concat;
use Tpetry\QueryExpressions\Value\Value;

pest()->group('v2', 'types');

it('generates a correct alias cast against a column')
    ->expect(new Alias('description', 'description_simple'))
    ->toBeExpression("\"description\"::pdb.alias('description_simple')");

it('generates a correct alias cast')
    ->expect(new Alias(new Concat(['description', new Value(' '), 'category']), 'description_concat'))
    ->toBeExpression("(\"description\"||' '||\"category\")::pdb.alias('description_concat')");
