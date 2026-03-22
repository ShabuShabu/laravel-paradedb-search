<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\ProxArray;
use ShabuShabu\ParadeDB\Expressions\v2\ProxRegex;

pest()->group('v2', 'expressions');

it('matches against an array of tokens')
    ->expect(new ProxArray(['sleek', 'white']))
    ->toBeExpression("pdb.prox_array('sleek', 'white')");

it('matches against an array of tokens incl a regex')
    ->expect(new ProxArray([new ProxRegex('sl.*'), 'white']))
    ->toBeExpression("pdb.prox_array(pdb.prox_regex(regex => 'sl.*'), 'white')");
