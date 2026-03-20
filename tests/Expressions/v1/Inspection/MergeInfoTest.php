<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Inspection\MergeInfo;

pest()->group('v1', 'inspection');

it('retrieves merge info')
    ->expect(new MergeInfo('teams_bm25_idx'))
    ->toBeExpression("paradedb.merge_info('teams_bm25_idx')");
