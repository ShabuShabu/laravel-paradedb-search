<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\v1;
use ShabuShabu\ParadeDB\Expressions\v1\Casts\JsonB;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

pest()->group('v1', 'integration');

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new v1\All)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('gets no results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new v1\Blank)
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
        ->select(['*', new v1\Score])
        ->where('id', '@@@', new v1\Boolean(
            should: [
                new v1\Boost(new v1\FuzzyTerm('name', 'test'), 2),
                new v1\FuzzyTerm('description', 'test'),
            ],
            must: [
                new v1\Range('created_at', new TimestampTz(null, now())),
            ],
            mustNot: [
                new v1\Range('deleted_at', new TimestampTz(null, now())),
            ],
        ))
        ->orderByDesc(new v1\Score)
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
        ->where('id', '@@@', new v1\Parse('description:test'))
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
        ->where('id', '@@@', new v1\ParseWithField('description', 'test'))
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
        ->selectWithSnippet('description')
        ->where('description', '@@@', 'test')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->snippet->toBe('<b>test</b> description');
});

it('searches for a given regular expression', function () {
    Team::factory()->create([
        'name' => 'first team',
        'description' => 'what?',
    ]);

    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Regex('description', '(something|test)'))
        ->orderBy('name')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('nice team')
        ->last()->name->toBe('test team');
});

it('checks for a field existence', function () {
    Team::factory()->softDeleted()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->whereSearch(new v1\Exists('deleted_at'))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});

it('searches for a given term', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Term('description', 'something'))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('searches for a given term set', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\TermSet([
            new v1\Term('description', 'something'),
            new v1\Term('description', 'other'),
        ]))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('searches for a fuzzy phrase', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\FullText('description', 'ruining shoes'))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('applies a constant score', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Boolean(
            should: [
                new v1\ConstScore(new v1\Term('description', 'shoes'), 1.0),
                new v1\Term('description', 'running'),
            ],
        ))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('searches for a phrase', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Phrase('description', ['running', 'shoes']))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('searches for a phrase prefix', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\PhrasePrefix('description', ['running', 'sh']))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('applies a disjunction max query', function () {
    Team::factory()->create([
        'name' => 'first team',
        'description' => 'boring description...',
    ]);

    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'running test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->selectWithScore()
        ->where('id', '@@@', new v1\DisjunctionMax([
            new v1\Term('description', 'shoes'),
            new v1\Term('description', 'running'),
        ]))
        ->orderByDesc(new v1\Score)
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('searches for a given range', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'max_members' => 3,
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'max_members' => 2,
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Range('max_members', new Int4(1, 3, Bounds::includeStartExcludeEnd)))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('test team');
});

it('searches for a given range term', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'size' => '[2,4)',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'size' => '[4,6)',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\RangeTerm('size', 3))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});

it('combines paradedb functions with regular eloquent wheres', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other...',
    ]);

    $teams = Team::query()
        ->where('id', '@@@', new v1\Parse('description:test'))
        ->whereLike('name', 'nice%')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});
