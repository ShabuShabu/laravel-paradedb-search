<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

pest()->group('commands');

it('verifies an index', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'verify')
        ->expectsChoice('Which index would you like to select?')
        ->expectsConfirmation('Do you want to verify that all indexed entries still exist in the heap table?')
        ->expectsQuestion('Enter a sample rate or leave empty.', '0.8')
        ->expectsConfirmation('Do you want to enable progress reporting to see status updates?')
        ->expectsConfirmation('Do you want to stop verification immediately when the first error is found?')
        ->expectsConfirmation('Do you want to enable verbose logging?')
        ->expectsChoice('Which segment ids would you like to verify?')
        ->assertExitCode(0);
})->todo();

it('verifies all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'verify-all')
        ->assertExitCode(0);
})->todo();

it('lists all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'indexes')
        ->assertExitCode(0);
})->todo();

it('lists all segments', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'segments')
        ->assertExitCode(0);
})->todo();
