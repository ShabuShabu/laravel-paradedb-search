<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

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
})->skip('Times out when all tests are run...');

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
})->skip('Times out when all tests are run...');

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
})->skip('Times out when all tests are run...');

it('performs a hybrid search', function () {
})->todo();

it('combines paradedb and eloquent queries', function () {
})->todo();

it('combines paradedb and eloquent queries in a sub query', function () {
})->todo();
