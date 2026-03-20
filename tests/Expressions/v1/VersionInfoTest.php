<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\VersionInfo;

pest()->group('v1');

it('retrieves some version info')
    ->expect(new VersionInfo)
    ->toBeExpression('paradedb.version_info()');
