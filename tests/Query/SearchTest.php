<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Distance;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\Query\Expressions\Similarity;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

it('gets search results', function () {
    Team::factory()->isVip(false)->create();

    $vipTeam = Team::factory()->isVip()->create();

    $results = Team::search()
        ->select(['*'])
        ->where(
            Builder::make()->whereFilter('is_vip', '=', true)
        )
        ->stableSort()
        ->alias('s')
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->sole()->id->toBe($vipTeam->id);
});

it('paginates search results', function () {
    Team::factory()->count(12)->isVip()->create();

    $results = Team::search()->where(
        Builder::make()->whereFilter('is_vip', '=', true)
    )->simplePaginate(8);

    expect($results)
        ->toBeInstanceOf(Paginator::class)
        ->count()->toBe(8)
        ->hasMorePages()->toBeTrue();
});

it('modifies the search query', function () {
    Team::factory()->count(12)->isVip()->create();

    $results = Team::search()
        ->modifyQueryUsing(fn (Eloquent\Builder $builder) => $builder->with('user'))
        ->where(Builder::make()->whereFilter('is_vip', '=', true))
        ->get();

    expect($results->first()->relationLoaded('user'))->toBeTrue();
});

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
});

it('performs a hybrid search', function () {
    Team::factory()->isVip(false)->withEmbedding([7, 8, 9])->create();

    $vipTeam = Team::factory()->isVip()->withEmbedding([1, 2, 3])->create();

    $results = Team::search()
        ->where(Builder::make()->whereFilter('is_vip', '=', true))
        ->where(new Similarity('embedding', Distance::l2, [1, 2, 3]))
        ->bm25Limit(100)
        ->bm25Weight(0.9)
        ->similarityLimit(100)
        ->similarityWeight(0.6)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->id->toBe($vipTeam->id)
        ->first()->hasAttribute('rank_hybrid')->toBeTrue();
});

it('modifies the query for a hybrid search', function () {
    Team::factory()->isVip(false)->withEmbedding([7, 8, 9])->create();
    Team::factory()->isVip()->withEmbedding([1, 2, 3])->create();

    $results = Team::search()
        ->modifyQueryUsing(fn (Eloquent\Builder $builder) => $builder->with('user'))
        ->where(Builder::make()->whereFilter('is_vip', '=', true))
        ->where(new Similarity('embedding', Distance::l2, [1, 2, 3]))
        ->get();

    expect($results->first()->relationLoaded('user'))->toBeTrue();
});

it('combines paradedb and eloquent queries', function () {
    Team::factory()->isVip(false)->maxMembers(6)->create();

    Team::factory()->isVip()->maxMembers(5)->create();

    $expected = Team::factory()->isVip()->maxMembers(6)->create();

    $subQuery = Team::search()
        ->where(Builder::make()->whereFilter('is_vip', '=', true))
        ->toBaseQuery();

    $results = Team::query()
        ->from($subQuery, 'idx')
        ->where('max_members', '>', 5)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->sole()->id->toBe($expected->id);
});

it('panics for a missing paradedb query', function () {
    Team::search()->toQuery();
})->throws(
    InvalidArgumentException::class,
    'Both hybrid and full-text search require a ParadeDB query'
);
