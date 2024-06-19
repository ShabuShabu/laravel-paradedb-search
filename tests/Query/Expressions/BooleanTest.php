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

it('builds a boolean query in a fluid manner', function (bool $when, string $expression) {
    $boolean = Boolean::query()
        ->must(Builder::make()->where('description', 'shoes'))
        ->should(new PhrasePrefix('description', ['book']))
        ->should([Builder::make()->where('description', 'blue')], $when)
        ->mustNot('category:electronics');

    expect($boolean)->toBeExpression($expression);
})->with([
    'true' => [true, "paradedb.boolean(must => ARRAY[paradedb.parse(query_string => 'description:shoes')], should => ARRAY[paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book'], max_expansion => NULL::integer), paradedb.parse(query_string => 'description:blue')], must_not => ARRAY[paradedb.parse(query_string => 'category:electronics')])"],
    'false' => [false, "paradedb.boolean(must => ARRAY[paradedb.parse(query_string => 'description:shoes')], should => ARRAY[paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book'], max_expansion => NULL::integer)], must_not => ARRAY[paradedb.parse(query_string => 'category:electronics')])"],
]);
