<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\Snippet;

it('highlights search queries: ', function (?string $startTag, ?string $endTag, ?int $maxNumChars, string $expression) {
    $snippet = ! is_null($maxNumChars)
        ? new Snippet('description', $startTag, $endTag, $maxNumChars)
        : new Snippet('description');

    expect($snippet)->toBeExpression($expression);
})->with([
    'no options' => [null, null, null, 'paradedb.snippet(field => "description")'],
    'with options' => ['<pre>', '</pre>', 10, "paradedb.snippet(field => \"description\", start_tag => '<pre>', end_tag => '</pre>', max_num_chars => 10)"],
]);

it('highlights search queries with custom tags', function () {
    config(['paradedb-search.highlighting_tag' => '<pre></pre>']);

    expect(new Snippet('description'))->toBeExpression(
        "paradedb.snippet(field => \"description\", start_tag => '<pre>', end_tag => '</pre>')"
    );
});
