<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\Inspection\VersionInfo;

pest()->group('v1', 'inspection');

it('retrieves some version info')
    ->expect(new VersionInfo)
    ->toBeExpression('paradedb.version_info()');
