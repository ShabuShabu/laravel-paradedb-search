<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

it('verifies an index', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity verify')
        ->assertExitCode(0);
})->todo();

it('verifies all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity verify-all')
        ->assertExitCode(0);
})->todo();

it('lists all indexes', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity indexes')
        ->assertExitCode(0);
})->todo();

it('lists all segments', function () {
    /* @phpstan-ignore variable.undefined */
    $this->artisan('paradedb:integrity segments')
        ->assertExitCode(0);
})->todo();
