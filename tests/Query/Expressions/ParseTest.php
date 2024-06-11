<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Parse;

it('parses a string query')
    ->expect(new Parse('description:shoes'))
    ->toBeExpression("paradedb.parse(query_string => 'description:shoes')");

it('parses a builder query')
    ->expect(new Parse(Builder::make()->where('description', 'shoes')))
    ->toBeExpression("paradedb.parse(query_string => 'description:shoes')");
