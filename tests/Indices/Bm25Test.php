<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Indices\Bm25;

beforeEach(function () {
    Bm25::index('teams')->drop();
});

afterEach(function () {
    create_teams_index();
});

it('creates and drops a partial bm25 index', function () {
    $result = Bm25::index('teams')
        ->partialBy('max_members > 2')
        ->addNumericFields(['max_members'])
        ->addBooleanFields(['is_vip'])
        ->addDateFields(['created_at'])
        ->addJsonFields(['options'])
        ->addRangeFields(['size'])
        ->addTextFields([
            'name',
            'description' => [
                'tokenizer' => [
                    'type' => 'default',
                ],
            ],
        ])
        ->create(drop: true);

    expect('teams_idx')->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();

    $result = Bm25::index('teams')->drop();

    expect('teams_idx')->not->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();
});

it('creates and deletes a bm25 index', function () {
    config(['paradedb-search.index_suffix' => '_index']);

    $result = Bm25::index('teams')
        ->addNumericFields(['max_members'])
        ->addBooleanFields(['is_vip'])
        ->addDateFields(['created_at'])
        ->addJsonFields(['options'])
        ->addRangeFields(['size'])
        ->addTextFields([
            'name',
            'description' => [
                'tokenizer' => [
                    'type' => 'default',
                ],
            ],
        ])
        ->create(drop: true);

    expect('teams_index')->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();

    $result = Bm25::index('teams')->drop();

    expect('teams_index')->not->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();
});

it('creates and deletes a bm25 index with a custom name', function () {
    $result = Bm25::index('teams')
        ->name('teams_test_idx')
        ->addNumericFields(['max_members'])
        ->addBooleanFields(['is_vip'])
        ->addDateFields(['created_at'])
        ->addJsonFields(['options'])
        ->addRangeFields(['size'])
        ->addTextFields([
            'name',
            'description' => [
                'tokenizer' => [
                    'type' => 'default',
                ],
            ],
        ])
        ->create(drop: true);

    expect('teams_test_idx')->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();

    $result = Bm25::index('teams')
        ->name('teams_test_idx')
        ->drop();

    expect('teams_test_idx')->not->toBeIndex(table: 'teams')
        ->and($result)->toBeTrue();
});

it('panics for an unknown field', function () {
    /** @noinspection PhpUndefinedMethodInspection */
    Bm25::index('teams')->addIntegerFields(['max_members']);
})->throws(
    InvalidArgumentException::class,
    'Field `integer` does not exist'
);
