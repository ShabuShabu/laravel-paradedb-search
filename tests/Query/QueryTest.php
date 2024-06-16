<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\Blank;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\Boost;
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Query\Expressions\Range;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::search()->where(new All())->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('gets no results', function () {
    Team::factory()->count(2)->create();

    $results = Team::search()->where(new Blank())->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(0);
});

it('performs a ranked boolean query with various conditions', function () {
    Team::factory()->softDeleted()->create();

    Team::factory()->create([
        'name' => 'Test team',
        'description' => 'Something or other...',
    ]);

    Team::factory()->create([
        'name' => 'Nice team',
        'description' => 'testing...',
    ]);

    $searchTerm = 'test';

    $results = Team::search()
        ->select(['*', new Rank('id')])
        ->where(new Boolean(
            must: [
                new Range('created_at', new TimestampTz(null, now())),
            ],
            should: [
                new Boost(new FuzzyTerm('name', $searchTerm), 2),
                new FuzzyTerm('description', $searchTerm),
            ],
            mustNot: [
                new Range('deleted_at', new TimestampTz(null, now())),
            ],
        ))
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('Test team')
        ->last()->name->toBe('Nice team');
});

it('adds a constant score', function () {
})->todo();

it('performs a disjunction max query', function () {
})->todo();

it('highlights search results', function () {
})->todo();

it('parses a query', function () {
})->todo();

it('performs a phrase prefix query', function () {
})->todo();

it('searches for a phrase', function () {
})->todo();

it('performs a regex query', function () {
})->todo();

it('searches for a set of terms', function () {
})->todo();

it('searches for a terms', function () {
})->todo();
