<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('lists tokenizers', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:tokenizers')
        ->expectsOutputToContain('These tokenizers are available:')
        ->assertExitCode(0);
});
