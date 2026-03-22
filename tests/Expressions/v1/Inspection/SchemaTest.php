<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Inspection\Schema;

pest()->group('v1', 'inspection');

it('retrieves schema info')
    ->expect(new Schema('teams_bm25_idx'))
    ->toBeExpression("paradedb.schema('teams_bm25_idx')");
