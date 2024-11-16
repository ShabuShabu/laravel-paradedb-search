<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\ParseWithField;

it('parses a string query for a field')
    ->expect(new ParseWithField('description', 'speaker bluetooth'))
    ->toBeExpression("paradedb.parse_with_field(field => 'description', query_string => 'speaker bluetooth', lenient => NULL::boolean, conjunction_mode => NULL::boolean)");
