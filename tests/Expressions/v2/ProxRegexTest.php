<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\ProxRegex;

pest()->group('v2', 'expressions');

it('matches against a regular expression')
    ->expect(new ProxRegex('sl.*'))
    ->toBeExpression("pdb.prox_regex(regex => 'sl.*')");

it('matches against a regular expression with options')
    ->expect(new ProxRegex('sl.*', 100))
    ->toBeExpression("pdb.prox_regex(regex => 'sl.*', max_expansions => 100)");
