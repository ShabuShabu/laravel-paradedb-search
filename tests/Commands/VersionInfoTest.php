<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

pest()->group('commands');

it('lists tokenizers', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:version')
        ->expectsOutputToContain('Version')
        // ->expectsPromptsTable(['Version', 'Git hash', 'Build mode'], [['0.22.0', '2b42b2d4b356bcf441884b97f88c1f7df1a25a20', 'release']])
        ->assertExitCode(0);
});
