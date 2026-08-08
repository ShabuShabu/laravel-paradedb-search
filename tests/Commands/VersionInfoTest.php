<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

pest()->group('commands');

it('shows the version', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:version')
        ->expectsOutputToContain('Version')
        // ->expectsPromptsTable(['Version', 'Build mode'], [['0.22.0', 'release']])
        ->assertExitCode(0);
});
