<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Boost;
use ShabuShabu\ParadeDB\Expressions\v2\Casts\Fuzzy;
use ShabuShabu\ParadeDB\Expressions\v2\Regex;

pest()->group('v2', 'casts');

it('boosts a column')
    ->expect(new Boost('description', 2))
    ->toBeExpression('"description"::pdb.boost(2)');

it('boosts a type cast')
    ->expect(new Boost(new Fuzzy('description', 2), 2))
    ->toBeExpression('"description"::pdb.fuzzy(2, f, f)::pdb.boost(2)');

it('boosts a query function')
    ->expect(new Boost(new Regex('key.*'), 2))
    ->toBeExpression("pdb.regex('key.*')::pdb.boost(2)");
