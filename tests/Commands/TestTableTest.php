<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('creates and drops the test table', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:test-table')
        ->expectsQuestion('What action would you like to perform?', 'create')
        ->expectsQuestion('What is the name of the test table?', 'bm25_test_table')
        ->expectsQuestion('What is the schema for the table?', 'paradedb')
        ->expectsOutputToContain('paradedb.bm25_test_table has been successfully created')
        ->assertExitCode(0);

    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:test-table')
        ->expectsQuestion('What action would you like to perform?', 'drop')
        ->expectsQuestion('What is the name of the test table?', 'bm25_test_table')
        ->expectsQuestion('What is the schema for the table?', 'paradedb')
        ->expectsOutputToContain('paradedb.bm25_test_table has been successfully dropped')
        ->assertExitCode(0);
});

it('panics for an existing table when creating', function () {
    Schema::create('paradedb.bm25_test_table', function () {});

    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:test-table')
        ->expectsQuestion('What action would you like to perform?', 'create')
        ->expectsQuestion('What is the name of the test table?', 'bm25_test_table')
        ->expectsQuestion('What is the schema for the table?', 'paradedb')
        ->expectsOutputToContain('The table paradedb.bm25_test_table exists already...')
        ->assertExitCode(2);
});

it('panics for a non-existing table when dropping', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:test-table')
        ->expectsQuestion('What action would you like to perform?', 'drop')
        ->expectsQuestion('What is the name of the test table?', 'bm25_test_table')
        ->expectsQuestion('What is the schema for the table?', 'paradedb')
        ->expectsOutputToContain('The table paradedb.bm25_test_table does not exist...')
        ->assertExitCode(2);
});

it('panics for a non-existing action', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:test-table')
        ->expectsQuestion('What action would you like to perform?', 'update')
        ->expectsQuestion('What is the name of the test table?', 'bm25_test_table')
        ->expectsQuestion('What is the schema for the table?', 'paradedb')
        ->expectsOutputToContain('The action update is not supported.')
        ->assertExitCode(1);
});
