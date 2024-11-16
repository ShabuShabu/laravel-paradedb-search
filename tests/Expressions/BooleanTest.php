<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Boolean;
use ShabuShabu\ParadeDB\Expressions\PhrasePrefix;
use ShabuShabu\ParadeDB\TantivyQL\Query;

it('filters documents based on logical relationships', function (string $type) {
    $queries = [
        Query::string()->where('description', 'shoes'),
        new PhrasePrefix('description', ['book']),
        'category:electronics',
    ];

    $query = "ARRAY[paradedb.parse(query_string => 'description:shoes'), paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book']), paradedb.parse(query_string => 'category:electronics')]";

    [$must, $should, $mustNot, $expression] = match ($type) {
        'must' => [$queries, null, null, "paradedb.boolean(must => $query)"],
        'should' => [null, $queries, null, "paradedb.boolean(should => $query)"],
        'must_not' => [null, null, $queries, "paradedb.boolean(must_not => $query)"],
    };

    expect(new Boolean($should, $must, $mustNot))->toBeExpression($expression);
})->with([
    'must' => ['must'],
    'should' => ['should'],
    'must_not' => ['must_not'],
]);

it('builds a boolean query in a fluid manner', function (bool $when, string $expression) {
    $boolean = Boolean::query()
        ->must(Query::string()->where('description', 'shoes'))
        ->should(new PhrasePrefix('description', ['book']))
        ->should([Query::string()->where('description', 'blue')], $when)
        ->mustNot('category:electronics');

    expect($boolean)->toBeExpression($expression);
})->with([
    'true' => [true, "paradedb.boolean(must => ARRAY[paradedb.parse(query_string => 'description:shoes')], should => ARRAY[paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book']), paradedb.parse(query_string => 'description:blue')], must_not => ARRAY[paradedb.parse(query_string => 'category:electronics')])"],
    'false' => [false, "paradedb.boolean(must => ARRAY[paradedb.parse(query_string => 'description:shoes')], should => ARRAY[paradedb.phrase_prefix(field => 'description', phrases => ARRAY['book'])], must_not => ARRAY[paradedb.parse(query_string => 'category:electronics')])"],
]);
