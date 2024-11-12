<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Distance;
use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\Expressions\Similarity;
use ShabuShabu\ParadeDB\Query\Expressions\HybridSearch;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('performs a hybrid search', function (string | Query | ParadeExpression $bm25Query) {
    expect(new HybridSearch('teams_idx', $bm25Query, new Similarity('embedding', Distance::l2, [1, 2, 3])))->toBeExpression(
        "teams_idx.rank_hybrid(bm25_query => paradedb.parse(query_string => 'description:shoes'), similarity_query => '\"embedding\" <-> ''[1,2,3]''', bm25_weight => 0.5, similarity_weight => 0.5, bm25_limit_n => 100, similarity_limit_n => 100)"
    );
})->with([
    'with builder' => [Query::string()->where('description', 'shoes')],
    'with expression' => [new Parse('description:shoes')],
    'with string' => ['description:shoes'],
]);
