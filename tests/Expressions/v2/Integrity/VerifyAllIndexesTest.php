<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Integrity\VerifyAllIndexes;

pest()->group('v2', 'integrity');

it('verifies all indexes')
    ->expect(new VerifyAllIndexes)
    ->toBeExpression('pdb.verify_all_indexes()');

it('verifies all indexes with options')
    ->expect(new VerifyAllIndexes(
        schemaPattern: 'public',
        indexPattern: 'search_%',
        heapAllIndexed: true,
        sampleRate: 0.8,
        reportProgress: true,
        onErrorStop: true,
    ))
    ->toBeExpression("pdb.verify_all_indexes(schema_pattern => 'public', index_pattern => 'search_%', heapallindexed => true, sample_rate => 0.8, report_progress => true, on_error_stop => true)");
