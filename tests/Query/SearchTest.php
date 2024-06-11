<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use ShabuShabu\ParadeDB\Indices\Bm25;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

/*
beforeEach(function () {
    Bm25::index('teams')
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
});

afterEach(function () {
    Bm25::index('teams')->drop();
});
*/

it('gets search results', function () {
    Team::factory()->isVip(false)->create();

    $vipTeam = Team::factory()->isVip()->create();

    $results = Team::search()->where(
        Builder::make()->whereFilter('is_vip', '=', true)
    )->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->sole()->id->toBe($vipTeam->id);
})->skip('Causes the test suite to time out like 99% of the time. Passes when run as a single test... sigh...');

it('paginates search results', function () {
    Team::factory()->isVip(false)->create();

    $vipTeam = Team::factory()->isVip()->create();

    $results = Team::search()->where(
        Builder::make()->whereFilter('is_vip', '=', true)
    )->simplePaginate();

    expect($results)
        ->toBeInstanceOf(Paginator::class)
        ->count()->toBe(1)
        ->getCollection()->sole()->id->toBe($vipTeam->id);
})->skip('Causes the test suite to time out like 99% of the time. Passes when run as a single test... sigh...');

it('gets search results with an eloquent query', function () {
    Team::factory()->isVip(false)->create();

    $vipTeam = Team::factory()->isVip()->create();

    $results = Team::query()->from(
        new FullTextSearch(
            index: 'teams_idx',
            query: Builder::make()->whereFilter('is_vip', '=', true),
        )
    )->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->sole()->id->toBe($vipTeam->id);
})->skip('Causes the test suite to time out like 99% of the time. Passes when run as a single test... sigh...');
