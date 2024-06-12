<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('asks for help', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:help', ['--dry' => true])
        ->expectsConfirmation('This command will eventually create a new GitHub discussion for the ParadeDB team. Are you sure you want to continue?', 'yes')
        ->expectsQuestion('Subject', 'I need help')
        ->expectsQuestion('Body', 'Not entirely sure what the issue is, tho...')
        ->expectsOutputToContain('Here is the result of the dry-run:')
        ->assertExitCode(0);
})->skip('Times out when all tests are run...');

it('cancels the help request', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:help', ['--dry' => true])
        ->expectsConfirmation('This command will eventually create a new GitHub discussion for the ParadeDB team. Are you sure you want to continue?')
        ->expectsOutputToContain('You have cancelled the request to create a new GitHub discussion!')
        ->assertExitCode(2);
})->skip('Times out when all tests are run...');
