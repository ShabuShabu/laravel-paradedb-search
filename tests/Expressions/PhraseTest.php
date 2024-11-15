<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Phrase;

it('searches for exact matches')
    ->expect(new Phrase('description', ['robot', 'building', 'kit']))
    ->toBeExpression("paradedb.phrase(field => 'description', phrases => ARRAY['robot', 'building', 'kit'], slop => NULL::integer)");

it('searches for exact matches with slop enabled')
    ->expect(new Phrase('description', ['robot', 'building', 'kit'], 1))
    ->toBeExpression("paradedb.phrase(field => 'description', phrases => ARRAY['robot', 'building', 'kit'], slop => 1)");
