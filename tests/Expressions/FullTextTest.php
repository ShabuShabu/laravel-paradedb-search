<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\FullText;

it('performs a match query: ', function (?string $tokenizer, ?int $distance, ?bool $transposeCostOne, ?bool $prefix, ?bool $conjunctionMode, string $expression) {
    expect(new FullText('description', 'wolo', $tokenizer, $distance, $transposeCostOne, $prefix, $conjunctionMode))->toBeExpression($expression);
})->with([
    'no options' => [null, null, null, null, null, "paradedb.match(field => 'description', value => 'wolo')"],
    'with options' => ['whitespace', 2, true, true, true, "paradedb.match(field => 'description', value => 'wolo', tokenizer => paradedb.tokenizer(name => 'whitespace'), distance => 2, tranposition_cost_one => true, prefix => true, conjunction_mode => true)"],
]);
