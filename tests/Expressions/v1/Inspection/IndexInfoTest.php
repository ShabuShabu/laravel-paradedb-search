<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Inspection\IndexInfo;

pest()->group('v1', 'inspection');

it('retrieves index info')
    ->expect(new IndexInfo('teams_bm25_idx'))
    ->toBeExpression("paradedb.index_info(index => 'teams_bm25_idx')");

it('retrieves index info with options')
    ->expect(new IndexInfo('teams_bm25_idx', true))
    ->toBeExpression("paradedb.index_info(index => 'teams_bm25_idx', show_invisible => true)");
