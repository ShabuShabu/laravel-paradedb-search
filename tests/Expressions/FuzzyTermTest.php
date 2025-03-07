<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\FuzzyTerm;

it('gets documents matching a fuzzy term: ', function (?int $distance, ?bool $cost, ?bool $prefix, string $expression) {
    expect(new FuzzyTerm('description', 'wolo', $distance, $cost, $prefix))->toBeExpression($expression);
})->with([
    'no options' => [null, null, null, "paradedb.fuzzy_term(field => 'description', value => 'wolo')"],
    'with options' => [1, false, true, "paradedb.fuzzy_term(field => 'description', value => 'wolo', distance => 1, tranposition_cost_one => false, prefix => true)"],
]);
