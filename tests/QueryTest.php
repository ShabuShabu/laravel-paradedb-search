<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Blank;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\Boost;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\JsonB;
use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\Expressions\ParseWithField;
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\Snippet;
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

it('performs a boosted boolean query with various conditions', function () {
    Team::factory()->softDeleted()->create();

    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test something...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->select(['*', new Score])
        ->where('id', '@@@', new Boolean(
            should: [
                new Boost(new FuzzyTerm('name', 'test'), 2),
                new FuzzyTerm('description', 'test'),
            ],
            must: [
                new Range('created_at', new TimestampTz(null, now())),
            ],
            mustNot: [
                new Range('deleted_at', new TimestampTz(null, now())),
            ],
        ))
        ->orderByDesc(new Score)
        ->paginate();

    expect($teams)
        ->toBeInstanceOf(LengthAwarePaginator::class)
        ->count()->toBe(2)
        ->first()->name->toBe('test team')
        ->last()->name->toBe('nice team');
});

it('searches for a given json query string', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new JsonB([
            'term' => [
                'field' => 'description',
                'value' => 'something',
            ],
        ]))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('parses a query string', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new Parse('description:test'))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});

it('parses a query string for a given field', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->whereSearch(new ParseWithField('description', 'test'))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});

it('highlights a search term', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    $teams = Team::query()
        ->select(['id', new Snippet('description')])
        ->where('description', '@@@', 'test')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->snippet->toBe('<b>test</b> description');
});

it('searches for a fuzzy phrase', function () {})->todo();

it('applies a constant score', function () {})->todo();

it('applies a disjunction max query', function () {})->todo();

it('checks for a field existence', function () {})->todo();

it('gets more like this', function () {})->todo();

it('searches for a phrase', function () {})->todo();

it('searches for a phrase prefix', function () {})->todo();

it('searches for a given range', function () {})->todo();

it('searches for a given range term', function () {})->todo();

it('applies a rank', function () {})->todo();

it('searches for a given regular expression', function () {})->todo();

it('searches for a given term', function () {})->todo();

it('searches for a given term set', function () {})->todo();

it('combines paradedb and eloquent queries', function () {})->todo();
