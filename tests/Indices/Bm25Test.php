<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Indices\Bm25;

it('creates and deletes a bm25 index', function () {
    config(['paradedb-search.table_suffix' => '_index']);

    $result = Bm25::index('teams')
        ->addNumericFields(['max_members'])
        ->addBooleanFields(['is_vip'])
        ->addDateFields(['created_at'])
        ->addJsonFields(['options'])
        ->addTextFields([
            'name',
            'description' => [
                'tokenizer' => [
                    'type' => 'default',
                ],
            ],
        ])
        ->create(drop: true);

    expect('teams_index')->toBeSchema()
        ->and($result)->toBeTrue();

    $result = Bm25::index('teams')->drop();

    expect('teams_index')->not->toBeSchema()
        ->and($result)->toBeTrue();
})->skip('Causes the test suite to time out like 99% of the time...');

it('panics for an unknown field', function () {
    /** @noinspection PhpUndefinedMethodInspection */
    Bm25::index('teams')->addIntegerFields(['max_members']);
})->throws(
    InvalidArgumentException::class,
    'Field `integer` does not exist'
);