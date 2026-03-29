<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\v2;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Operators\FullText;
use ShabuShabu\ParadeDB\TantivyQL\Query;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;
use Tpetry\QueryExpressions\Language\Alias;

pest()->group('v2', 'integration');

it('can use all available operators', function (FullText | Distance $operator) {
    Team::search()
        ->where('description', $operator, 'test')
        ->get();
})->with([
    ...FullText::cases(),
    ...Distance::cases(),
])->throwsNoExceptions();

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::search()
        ->whereQuery('id', new v2\All)
        ->get();

    expect($results)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2);
});

it('performs an aggregate query', function () {
    Team::factory()->count(2)->create();

    $result = Team::search()
        ->select(new v2\Agg(['value_count' => ['field' => 'id']]))
        ->whereQuery('id', new v2\All)
        ->agg();

    expect($result->first()->agg->value)->toBe(2.0);
});

it('performs a more like this query', function () {
    Team::factory()->count(2)->create();

    $team1 = Team::factory()->create([
        'description' => 'this is just a test',
    ]);

    $team2 = Team::factory()->create([
        'description' => 'this is another test',
    ]);

    $teams = Team::search()
        ->withScore()
        ->where('id', '@@@', new v2\MoreLikeThis($team1->id))
        ->where('id', '!=', $team1->id)
        ->orderByDesc(new v2\Score)
        ->get();

    expect($teams->first()->id)->toBe($team2->id);
});

it('parses a tantivy query', function () {
    Team::factory()->create([
        'description' => 'blablabla',
    ]);

    $team = Team::factory()->create([
        'description' => 'this is just a test',
    ]);

    $result = Team::search()
        ->whereQuery('id', Query::string()->where('description', 'test'))
        ->get();

    expect($result->first()->id)->toBe($team->id);
});

it('performs a phrase prefix query', function () {
    Team::factory()->count(2)->create();

    $team = Team::factory()->create([
        'description' => 'We have a lot of running shoes...',
    ]);

    $result = Team::search()
        ->whereQuery('description', new v2\PhrasePrefix(['running', 'sh']))
        ->get();

    expect($result->sole()->id)->toBe($team->id);
});

it('performs a regex query', function () {
    Team::factory()->count(2)->create();

    $team = Team::factory()->create([
        'description' => 'We have a lot of keyrings',
    ]);

    $result = Team::search()
        ->whereQuery('description', new v2\Regex('key.*'))
        ->get();

    expect($result->sole()->id)->toBe($team->id);
});

it('performs a regex phrase query', function () {
    Team::factory()->count(2)->create();

    $team = Team::factory()->create([
        'description' => 'We have a lot of running shoes...',
    ]);

    $result = Team::search()
        ->whereQuery('description', new v2\RegexPhrase(['ru.*', 'shoes']))
        ->get();

    expect($result->sole()->id)->toBe($team->id);
});

it('performs a proximity query', function () {})->todo();

it('performs a proximity array query', function () {})->todo();

it('performs a proximity regex query', function () {})->todo();

it('performs a range term query', function () {})->todo();

it('scores a query result', function () {})->todo();

it('highlights a search term', function () {})->todo();

it('highlights multiple search terms', function () {})->todo();

it('gets snippet positions', function () {})->todo();

it('applies a rank', function () {
    Team::factory()->create([
        'name' => 'nice team',
        'description' => 'something...',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'description' => 'something or another something...',
    ]);

    $teams = Team::search()
        ->select([
            'name',
            new Alias(new v2\Support\Rank([new v2\Score, 'asc']), 'rank'),
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
        'embedding' => '[1,2,3]',
    ]);

    Team::factory()->create([
        'name' => 'test team',
        'embedding' => '[2,3,4]',
    ]);

    $teams = Team::search()
        ->select([
            'name',
            new Alias(new v2\Support\Rank([new v2\Similarity('embedding', Distance::cosine, [1, 2, 3]), 'asc']), 'rank'),
        ])
        ->orderBy(new v2\Similarity('embedding', Distance::cosine, [1, 2, 3]))
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(2)
        ->first()->name->toBe('nice team')
        ->last()->name->toBe('test team');
});
