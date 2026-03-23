<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Tpetry\QueryExpressions\Value\Value;
use ShabuShabu\ParadeDB\Expressions\v2\Support\TextArray;

pest()->group('v2', 'support');

it('generates an array of strings')
    ->expect(new TextArray(['running', 'shoes']))
    ->toBeExpression("ARRAY['running', 'shoes']");

it('filters out anything but strings')
    ->expect(new TextArray([true, 'running', null, 'shoes', 3, new Value('foo')]))
    ->toBeExpression("ARRAY['running', 'shoes']");
