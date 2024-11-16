<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\All;
use ShabuShabu\ParadeDB\Expressions\Blank;
use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\Boost;
use ShabuShabu\ParadeDB\Expressions\ConstScore;
use ShabuShabu\ParadeDB\Expressions\DisjunctionMax;
use ShabuShabu\ParadeDB\Expressions\Exists;
use ShabuShabu\ParadeDB\Expressions\FuzzyPhrase;
use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;
use ShabuShabu\ParadeDB\Expressions\JsonB;
use ShabuShabu\ParadeDB\Expressions\MoreLikeThis;
use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\Expressions\ParseWithField;
use ShabuShabu\ParadeDB\Expressions\Phrase;
use ShabuShabu\ParadeDB\Expressions\PhrasePrefix;
use ShabuShabu\ParadeDB\Expressions\Range;
use ShabuShabu\ParadeDB\Expressions\Ranges\Bounds;
use ShabuShabu\ParadeDB\Expressions\Ranges\Int4;
use ShabuShabu\ParadeDB\Expressions\Ranges\TimestampTz;
use ShabuShabu\ParadeDB\Expressions\RangeTerm;
use ShabuShabu\ParadeDB\Expressions\Rank;
use ShabuShabu\ParadeDB\Expressions\Regex;
use ShabuShabu\ParadeDB\Expressions\Score;
use ShabuShabu\ParadeDB\Expressions\Similarity;
use ShabuShabu\ParadeDB\Expressions\Snippet;
use ShabuShabu\ParadeDB\Expressions\Term;
use ShabuShabu\ParadeDB\Expressions\TermSet;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;
use ShabuShabu\ParadeDB\Tests\App\Models\User;
use Tpetry\QueryExpressions\Language\Alias;

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
        ->whereSearch(new Regex('description', '(something|test)'))
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
        ->whereSearch(new Exists('deleted_at'))
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
        ->whereSearch(new Term('description', 'something'))
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
        ->whereSearch(new TermSet([
            new Term('description', 'something'),
            new Term('description', 'other'),
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
        ->whereSearch(new FuzzyPhrase('description', 'ruining shoez'))
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
        ->where('id', '@@@', new Boolean(
            should: [
                new ConstScore(new Term('description', 'shoes'), 1.0),
                new Term('description', 'running'),
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
        ->whereSearch(new Phrase('description', ['running', 'shoes']))
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
        ->whereSearch(new PhrasePrefix('description', ['running', 'sh']))
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
        ->select(['*', new Score])
        ->whereSearch(new DisjunctionMax([
            new Term('description', 'shoes'),
            new Term('description', 'running'),
        ]))
        ->orderByDesc(new Score)
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('test team')
        ->last()->name->toBe('nice team');
});

// @todo Should probably return a result
it('gets more like this by id', function () {
    $user = User::factory()->create();

    $first = Team::factory()->create([
        'user_id' => $user->id,
        'name' => 'first team',
        'description' => 'boring description...',
        'is_vip' => true,
        'max_members' => 3,
    ]);

    Team::factory()->create([
        'user_id' => $user->id,
        'name' => 'nice team',
        'description' => 'boring description...',
        'is_vip' => true,
        'max_members' => 3,
    ]);

    $teams = Team::query()
        ->whereSearch(new MoreLikeThis(
            idOrFields: $first->id,
            minTermFrequency: 1,
        ))
        ->get();

    expect($teams)->toBeInstanceOf(Collection::class);
});

// @todo Should probably return a result
it('gets more like this by search term,', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'running test description...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or other or maybe running shoes...',
    ]);

    $teams = Team::query()
        ->whereSearch(new MoreLikeThis(
            idOrFields: ['description' => 'running'],
            minDocFrequency: 0,
            maxDocFrequency: 100,
            minTermFrequency: 1,
            stopWords: ['and', 'or']
        ))
        ->get();

    expect($teams)->toBeInstanceOf(Collection::class);
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
        ->whereSearch(new Range('max_members', new Int4(1, 3, Bounds::includeStartExcludeEnd)))
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
        ->whereSearch(new RangeTerm('size', 3))
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
        ->where('id', '@@@', new Parse('description:test'))
        ->whereLike('name', 'nice%')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->name->toBe('nice team');
});

it('applies a rank', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'something...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or another something...',
    ]);

    $teams = Team::query()
        ->select([
            'name',
            new Alias(new Rank([new Score, 'asc']), 'rank'),
        ])
        ->where('description', '@@@', 'something')
        ->orderBy('rank')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('test team')
        ->last()->name->toBe('nice team');
});

it('performs a similarity search', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'something...',
        'embedding' => '[1,2,3]',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or another something...',
        'embedding' => '[2,3,4]',
    ]);

    $teams = Team::query()
        ->select([
            'name',
            new Alias(new Rank([new Similarity('embedding', Distance::cosine, [1, 2, 3]), 'asc']), 'rank'),
        ])
        ->orderBy(new Similarity('embedding', Distance::cosine, [1, 2, 3]))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('nice team')
        ->last()->name->toBe('test team');
});
