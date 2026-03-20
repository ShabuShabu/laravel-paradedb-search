<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Regex;

pest()->group('v2', 'expressions');

it('generates a correct regex expression')
    ->expect(new Regex('key.*'))
    ->toBeExpression("pdb.regex('key.*')");
