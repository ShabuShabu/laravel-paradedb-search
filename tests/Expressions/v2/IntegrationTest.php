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

    expect($teams->first()->id)->toBe($team2->id)
        ->and($teams->first()->score)->not->toBeNull();
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

it('performs a range term query', function () {
    Team::factory()->create([
        'size' => '[4,6)',
    ]);

    $team = Team::factory()->create([
        'size' => '[2,4)',
    ]);

    $result = Team::query()
        ->where('size', '@@@', new v2\RangeTerm(3))
        ->get();

    expect($result->sole()->id)->toBe($team->id);
});

it('performs a proximity query using a ', function (mixed $expression) {
    Team::factory()->count(2)->create();

    $team = Team::factory()->create([
        'description' => 'We have a lot of sleek running shoes...',
    ]);

    $result = Team::search()
        ->whereQuery('description', new v2\Proximity($expression, 1, 'shoes'))
        ->get();

    expect($result->sole()->id)->toBe($team->id);
})->with([
    'string' => ['sleek'],
    'regex expression' => [new v2\ProxRegex('sl.*')],
    'array expression' => [new v2\ProxArray([new v2\ProxRegex('sl.*'), 'white'])],
]);

it('highlights a search term', function () {
    Team::factory()->create([
        'description' => 'test description...',
    ]);

    $teams = Team::query()
        ->select(['*', new v2\Snippet('description')])
        ->where('description', '@@@', 'test')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->snippet->toBe('<b>test</b> description');
});

it('highlights multiple search terms', function () {
    Team::factory()->create([
        'description' => 'Comes with a ceramic vase made by an artistic dude.',
    ]);

    $teams = Team::query()
        ->select(['*', new v2\Snippets('description')])
        ->whereDisjunction('description', 'artistic vase')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->snippets->toBe('{"Comes with a ceramic <b>vase</b> made by an <b>artistic</b> dude"}');
});

it('gets snippet positions', function () {
    Team::factory()->create([
        'description' => 'White jogging shoes',
    ]);

    $teams = Team::query()
        ->select(['*', new v2\Snippet('description'), new v2\SnippetPositions('description')])
        ->where('description', '|||', 'shoes')
        ->get();

    expect($teams)
        ->toBeInstanceOf(Collection::class)
        ->count()->toBe(1)
        ->first()->snippet_positions->toBe('{{14,19}}');
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
