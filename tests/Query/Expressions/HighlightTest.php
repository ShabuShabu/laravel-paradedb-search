<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Query\Expressions\Highlight;

it('gets documents matching a fuzzy term: ', function (?string $prefix, ?string $postfix, ?int $chars, ?string $alias, string $expression) {
    expect(new Highlight('id', 'description', $prefix, $postfix, $chars, $alias))->toBeExpression($expression);
})->with([
    'no options' => [null, null, null, null, "paradedb.highlight(key => \"id\", field => 'description', prefix => NULL::text, postfix => NULL::text, max_num_chars => NULL::integer, alias => NULL::text)"],
    'with options' => ['<pre>', '</pre>', 10, 'test', "paradedb.highlight(key => \"id\", field => 'description', prefix => '<pre>', postfix => '</pre>', max_num_chars => 10, alias => 'test')"],
]);
