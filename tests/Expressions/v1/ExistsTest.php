<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Exists;

pest()->group('v1');

it('checks for field existence')
    ->expect(new Exists('rating'))
    ->toBeExpression("paradedb.exists(field => 'rating')");
