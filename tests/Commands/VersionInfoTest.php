<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('lists tokenizers', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:version')
        ->expectsOutputToContain('Version')
        //->expectsOutputToContain('Git hash')
        //->expectsOutputToContain('Build mode')
        ->assertExitCode(0);
});
