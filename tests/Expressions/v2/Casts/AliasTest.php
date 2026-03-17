<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Tpetry\QueryExpressions\Value\Value;
use ShabuShabu\ParadeDB\Expressions\v2\Casts\Alias;
use Tpetry\QueryExpressions\Function\String\Concat;

it('generates a correct alias cast')
    ->expect(new Alias(new Concat(['description', new Value(' '), 'category']), 'description_concat'))
    ->toBeExpression("(\"description\"||' '||\"category\")::pdb.alias('description_concat')");
