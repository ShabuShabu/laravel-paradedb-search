<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\v2\All;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

pest()->group('v2', 'integration');

it('can use all available operators')->todo();

it('performs an aggregate query')->todo();

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new All)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('performs a more like this query')->todo();

it('parses a tantivy query')->todo();

it('performs a phrase prefix query')->todo();

it('performs a proximity query')->todo();

it('performs a proximity array query')->todo();

it('performs a proximity regex query')->todo();

it('performs a range term query')->todo();

it('performs a regex phrase query')->todo();

it('performs a regex query')->todo();

it('scores a query result')->todo();

it('highlights a search term')->todo();

it('highlights multiple search terms')->todo();

it('gets snippet positions')->todo();
