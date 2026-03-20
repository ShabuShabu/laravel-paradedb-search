<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Integrity\IndexSegments;

pest()->group('v2', 'integrity');

it('gets all index segments')
    ->expect(new IndexSegments('teams_bm25_idx'))
    ->toBeExpression("pdb.index_segments('teams_bm25_idx')");
