<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Regex;

pest()->group('v1');

it('finds documents based on a regex expression')
    ->expect(new Regex('description', '(hardcover|plush|leather|running|wireless)'))
    ->toBeExpression("paradedb.regex(field => 'description', pattern => '(hardcover|plush|leather|running|wireless)')");
