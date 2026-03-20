<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Integrity\VerifyIndex;

pest()->group('v2', 'integrity');

it('verifies an index')
    ->expect(new VerifyIndex('teams_bm25_idx'))
    ->toBeExpression("pdb.verify_index(index => 'teams_bm25_idx')");

it('verifies an index with options')
    ->expect(new VerifyIndex(
        index: 'teams_bm25_idx',
        heapAllIndexed: true,
        sampleRate: 0.8,
        reportProgress: true,
        verbose: true,
        onErrorStop: true,
        segmentIds: [1, 2],
    ))
    ->toBeExpression("pdb.verify_index(index => 'teams_bm25_idx', heapallindexed => true, sample_rate => 0.8, report_progress => true, verbose => true, on_error_stop => true, segment_ids => ARRAY[1, 2])");
