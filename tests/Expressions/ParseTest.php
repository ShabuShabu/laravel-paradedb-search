<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('parses a string query')
    ->expect(new Parse('description:shoes'))
    ->toBeExpression("paradedb.parse(query_string => 'description:shoes', lenient => NULL::boolean, conjunction_mode => NULL::boolean)");

it('parses a builder query')
    ->expect(new Parse(Query::string()->where('description', 'shoes')))
    ->toBeExpression("paradedb.parse(query_string => 'description:shoes', lenient => NULL::boolean, conjunction_mode => NULL::boolean)");
