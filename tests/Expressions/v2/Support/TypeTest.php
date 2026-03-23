<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Support\Type;

pest()->group('v2', 'support');

it('creates a correct type expression')
    ->expect(new Type('description', 'boolean'))
    ->toBeExpression('"description" boolean');
