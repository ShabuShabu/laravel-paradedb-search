<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\All;
use ShabuShabu\ParadeDB\Query\Expressions\Blank;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\Boost;
use ShabuShabu\ParadeDB\Query\Expressions\ConstScore;
use ShabuShabu\ParadeDB\Query\Expressions\DisjunctionMax;
use ShabuShabu\ParadeDB\Query\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Query\Expressions\Highlight;
use ShabuShabu\ParadeDB\Query\Expressions\Phrase;
use ShabuShabu\ParadeDB\Query\Expressions\PhrasePrefix;
use ShabuShabu\ParadeDB\Query\Expressions\Range;
use ShabuShabu\ParadeDB\Query\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Query\Expressions\Rank;
use ShabuShabu\ParadeDB\Query\Expressions\Regex;
use ShabuShabu\ParadeDB\Query\Expressions\Term;
use ShabuShabu\ParadeDB\Query\Expressions\TermSet;
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
        ->where(new DisjunctionMax(Builder::make()->where('name', 'team')))
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('Test team');
});

it('highlights search results', function () {
    Team::factory()->create(['name' => 'Test team']);

    $results = Team::search()
        ->addSelect(new Highlight('id', 'name'))
        ->where(new DisjunctionMax(Builder::make()->where('name', 'team')))
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
