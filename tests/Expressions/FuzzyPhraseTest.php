<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\FuzzyPhrase;

it('gets documents matching a fuzzy phrase: ', function (?int $distance, ?bool $cost, ?bool $prefix, ?bool $matchAllTerms, string $expression) {
    expect(new FuzzyPhrase('description', 'wolo', $distance, $cost, $prefix, $matchAllTerms))->toBeExpression($expression);
})->with([
    'no options' => [null, null, null, null, "paradedb.fuzzy_phrase(field => 'description', value => 'wolo', distance => NULL::integer, tranposition_cost_one => NULL::boolean, prefix => NULL::boolean, match_all_terms => NULL::boolean)"],
    'with options' => [1, false, true, true, "paradedb.fuzzy_phrase(field => 'description', value => 'wolo', distance => 1, tranposition_cost_one => false, prefix => true, match_all_terms => true)"],
]);
