<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Casts\Row;

pest()->group('v2', 'casts');

it('generates a correct custom row cast')
    ->expect(new Row('item_fields', ['description', 'category', 'in_stock']))
    ->toBeExpression('ROW("description", "category", "in_stock")::item_fields');
