<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\Blank;

it('matches no documents')
    ->expect(new Blank())
    ->toBeExpression('paradedb.empty()');
