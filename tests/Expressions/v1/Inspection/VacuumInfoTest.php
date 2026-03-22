<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Inspection\VacuumInfo;

pest()->group('v1', 'inspection');

it('retrieves some vacuum info')
    ->expect(new VacuumInfo('teams_bm25_idx'))
    ->toBeExpression("paradedb.vacuum_info('teams_bm25_idx')");
