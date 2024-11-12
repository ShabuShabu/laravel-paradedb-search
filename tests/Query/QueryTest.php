<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Blank;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\Boost;
use ShabuShabu\ParadeDB\Expressions\ConstScore;
use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\Phrase;
use ShabuShabu\ParadeDB\Expressions\PhrasePrefix;
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\Regex;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\Snippet;
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\TermSet;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::search()->where(new All)->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('gets no results', function () {
    Team::factory()->count(2)->create();

    $results = Team::search()->where(new Blank)->get();

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
        ->select(['*', new Score('id')])
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
    Team::factory()->count(2)->create();

    $results = Team::search()
        ->where(new ConstScore(new All, 3.9))
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('performs a disjunction max query', function () {
    Team::factory()->count(2)->create();

    Team::factory()->create(['name' => 'Test team']);

    $results = Team::search()
        ->where(new DisjunctionMax(Query::string()->where('name', 'team')))
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('Test team');
});

it('highlights search results', function () {
    Team::factory()->create(['name' => 'Test team']);

    $results = Team::search()
        ->addSelect(new Snippet('id', 'name'))
        ->where(new DisjunctionMax(Query::string()->where('name', 'team')))
        ->get();

    expect($results->first())->highlight->toBe('Test <b>team</b>');
});

it('searches for a phrase', function () {
    Team::factory()->create();

    $team = Team::factory()->create([
        'description' => 'This team sells robot building kits among other things...',
    ]);

    $results = Team::search()
        ->where(new Phrase('description', ['robot', 'building', 'kits']))
        ->get();

    expect($results->first())->id->toBe($team->id);
});

it('performs a phrase prefix query', function () {
    Team::factory()->create();

    $team = Team::factory()->create([
        'description' => 'This team sells robot building kits among other things...',
    ]);

    $results = Team::search()
        ->where(new PhrasePrefix('description', ['robot', 'building', 'kits', 'am']))
        ->get();

    expect($results->first())->id->toBe($team->id);
});

it('performs a regex query', function () {
    Team::factory()->create();

    $team = Team::factory()->create([
        'description' => 'This team sells robot building kits among other things...',
    ]);

    $results = Team::search()
        ->where(new Regex('description', '(team|kits|blabla)'))
        ->get();

    expect($results->first())->id->toBe($team->id);
});

it('searches for a term', function () {
    Team::factory()->create();

    $team = Team::factory()->create([
        'description' => 'This team sells robot building kits among other things...',
    ]);

    $results = Team::search()
        ->where(new Term('description', 'building'))
        ->get();

    expect($results->first())->id->toBe($team->id);
});

it('searches for a set of terms', function () {
    Team::factory()->create();

    $team = Team::factory()->create([
        'description' => 'This team sells robot building kits among other things...',
    ]);

    $results = Team::search()
        ->where(new TermSet([
            new Term('description', 'building'),
            new Term('description', 'things'),
        ]))
        ->get();

    expect($results->first())->id->toBe($team->id);
});
