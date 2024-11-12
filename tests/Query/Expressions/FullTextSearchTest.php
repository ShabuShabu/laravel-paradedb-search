<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\ParadeExpression;
use ShabuShabu\ParadeDB\Expressions\Parse;
use ShabuShabu\ParadeDB\Query\Expressions\FullTextSearch;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('performs a full-text search: ', function (string | Query | ParadeExpression $query) {
    expect(new FullTextSearch('teams_idx', $query))->toBeExpression(
        "teams_idx.search(query => paradedb.parse(query_string => 'description:shoes'), offset_rows => NULL::integer, limit_rows => NULL::integer, alias => NULL::text, stable_sort => NULL::boolean)"
    );
})->with([
    'with builder' => [Query::string()->where('description', 'shoes')],
    'with expression' => [new Parse('description:shoes')],
    'with string' => ['description:shoes'],
]);

it('performs a full-text search with options', function () {
    expect(new FullTextSearch('teams_idx', 'description:shoes', 40, 20, 'test', true))->toBeExpression(
        "teams_idx.search(query => paradedb.parse(query_string => 'description:shoes'), offset_rows => 20, limit_rows => 40, alias => 'test', stable_sort => true)"
    );
});
