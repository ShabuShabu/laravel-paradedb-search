<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use ShabuShabu\ParadeDB\Expressions\v2;
use ShabuShabu\ParadeDB\Operators\Distance;
use ShabuShabu\ParadeDB\Tests\App\Models\Team;
use Tpetry\QueryExpressions\Language\Alias;

pest()->group('v2', 'integration');

it('can use all available operators')->todo();

it('performs an aggregate query')->todo();

it('gets all results', function () {
    Team::factory()->count(2)->create();

    $results = Team::query()
        ->where('id', '@@@', new v2\All)
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

    $teams = Team::query()
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
