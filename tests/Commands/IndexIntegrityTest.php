<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

pest()->group('commands');

it('verifies an index', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'verify')
        // Not asked for only a single index
        //->expectsChoice('Which index would you like to select?', 'teams_bm25_idx', ['teams_bm25_idx' ])
        ->expectsConfirmation('Do you want to verify that all indexed entries still exist in the heap table?')
        ->expectsQuestion('Enter a sample rate or leave empty.', '0.8')
        ->expectsConfirmation('Do you want to enable progress reporting to see status updates?')
        ->expectsConfirmation('Do you want to stop verification immediately when the first error is found?')
        ->expectsConfirmation('Do you want to enable verbose logging?')
        // not asked for no segment ids
        //->expectsChoice('Which segment ids would you like to verify?', 0, [0])
        ->assertExitCode(0);
});

it('verifies all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'verify-all')
        ->expectsQuestion('Enter a schema name or pattern:', 'public')
        ->expectsQuestion('Enter an index name or pattern:', 'search_%')
        ->expectsConfirmation('Do you want to verify that all indexed entries still exist in the heap table?')
        ->expectsQuestion('Enter a sample rate or leave empty.', '0.8')
        ->expectsConfirmation('Do you want to enable progress reporting to see status updates?')
        ->expectsConfirmation('Do you want to stop verification immediately when the first error is found?')
        ->assertExitCode(0);
});

it('lists all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'indexes')
        ->expectsOutputToContain('Schema name')
        // ->expectsPromptsTable(
        //     ['Schema name', 'Table name', 'Index name', 'Index rel id', 'Num segments', 'Total docs'],
        //     [['public', 'teams', 'teams_bm25_idx', '19963666', '0', '0']],
        // )
        ->assertExitCode(0);
});

it('lists all segments', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity')
        ->expectsQuestion('What action would you like to perform?', 'segments')
        ->expectsOutputToContain('Partition name')
        ->assertExitCode(0);
});
