<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\ParadeQL\Builder;
use ShabuShabu\ParadeDB\Query\Expressions\Boolean;
use ShabuShabu\ParadeDB\Query\Expressions\PhrasePrefix;

it('filters documents based on logical relationships', function (string $type) {
    $queries = [
        Builder::make()->where('description', 'shoes'),
        new PhrasePrefix('description', ['book']),
        'category:electronics',
    ];

    $query = "ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book'], max_expansion => NULL::integer), paradedb.parse(query_string => 'category:electronics')]";

    [$must, $should, $mustNot, $expression] = match ($type) {
        'must' => [$queries, null, null, "paradedb.boolean(must => $query, should => ARRAY[]::paradedb.searchqueryinput[], must_not => ARRAY[]::paradedb.searchqueryinput[])"],
        'should' => [null, $queries, null, "paradedb.boolean(must => ARRAY[]::paradedb.searchqueryinput[], should => $query, must_not => ARRAY[]::paradedb.searchqueryinput[])"],
        'must_not' => [null, null, $queries, "paradedb.boolean(must => ARRAY[]::paradedb.searchqueryinput[], should => ARRAY[]::paradedb.searchqueryinput[], must_not => $query)"],
    };

    expect(new Boolean($must, $should, $mustNot))->toBeExpression($expression);
})->with([
    'must' => ['must'],
    'should' => ['should'],
    'must_not' => ['must_not'],
]);
