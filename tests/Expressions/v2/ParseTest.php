<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Parse;
use ShabuShabu\ParadeDB\TantivyQL\Query;

pest()->group('v2', 'expressions');

it('parses a string query')
    ->expect(new Parse('description:shoes'))
    ->toBeExpression("pdb.parse(query_string => 'description:shoes')");

it('parses a builder query')
    ->expect(new Parse(Query::string()->where('description', 'shoes'), true, false))
    ->toBeExpression("pdb.parse(query_string => 'description:shoes', lenient => true, conjunction_mode => false)");
