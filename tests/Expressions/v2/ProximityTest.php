<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\ProxArray;
use ShabuShabu\ParadeDB\Expressions\v2\Proximity;
use ShabuShabu\ParadeDB\Expressions\v2\ProxRegex;

pest()->group('v2', 'expressions');

it('matches based on token proximity')
    ->expect(new Proximity('sleek', 1, 'shoes'))
    ->toBeExpression("('sleek' ## 1 ## 'shoes')");

it('matches based on token proximity while enforcing order')
    ->expect(new Proximity('sleek', 1, 'shoes', true))
    ->toBeExpression("('sleek' ##> 1 ##> 'shoes')");

it('matches based on token proximity using regex')
    ->expect(new Proximity(new ProxRegex('sl.*'), 1, 'shoes'))
    ->toBeExpression("(pdb.prox_regex(regex => 'sl.*') ## 1 ## 'shoes')");

it('matches based on token proximity using a')
    ->expect(new Proximity(new ProxArray(['sleek', 'white']), 1, 'shoes'))
    ->toBeExpression("(pdb.prox_array('sleek', 'white') ## 1 ## 'shoes')");
