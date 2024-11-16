<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Blank;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\Boost;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new All)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('gets no results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new Blank)
        ->get();

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

    $results = Team::query()
        ->select(['*', new Score])
        ->where('id', '@@@', new Boolean(
            should: [
                new Boost(new FuzzyTerm('name', $searchTerm), 2),
                new FuzzyTerm('description', $searchTerm),
            ],
            must: [
                new Range('created_at', new TimestampTz(null, now())),
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

it('boosts a query', function () {});

it('applies a constant score', function () {});

it('applies a disjunction max query', function () {});

it('checks for a field existence', function () {});

it('searches for a fuzzy phrase', function () {});

it('searches for a fuzzy term', function () {});

it('searches for a given json query string', function () {});

it('gets more like this', function () {});

it('parses a query string', function () {});

it('parses a query string for a given field', function () {});

it('searches for a phrase', function () {});

it('searches for a phrase prefix', function () {});

it('searches for a given range', function () {});

it('searches for a given range term', function () {});

it('applies a rank', function () {});

it('searches for a given regular expression', function () {});

it('applies a score', function () {});

it('highlights a search term', function () {});

it('searches for a given term', function () {});

it('searches for a given term set', function () {});

it('paginates search results', function () {});

it('combines paradedb and eloquent queries', function () {});
