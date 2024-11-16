<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Exists;

it('checks for field existence')
    ->expect(new Exists('rating'))
    ->toBeExpression("paradedb.exists(field => 'rating')");
