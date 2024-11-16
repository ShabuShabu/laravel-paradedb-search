<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\JsonB;

it('creates a jsonb query string from an array')
    ->expect(new JsonB([
        'fuzzy_term' => [
            'field' => 'description',
            'value' => 'shoez',
        ],
    ]))
    ->toBeExpression('\'{"fuzzy_term":{"field":"description","value":"shoez"}}\'::jsonb');

it('creates a jsonb query string from a string')
    ->expect(new JsonB('{"fuzzy_term":{"field":"description","value":"shoez"}}'))
    ->toBeExpression('\'{"fuzzy_term":{"field":"description","value":"shoez"}}\'::jsonb');

it('panics for an invalid json string', function () {
    (new JsonB('invalid json string'))->getValue(grammar());
})->throws(JsonException::class);
