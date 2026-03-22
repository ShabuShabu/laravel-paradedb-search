<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\Agg;

pest()->group('v2', 'expressions');

it('generates a correct aggregate from an array')
    ->expect(new Agg(['value_count' => ['field' => 'id']]))
    ->toBeExpression('pdb.agg(\'{"value_count":{"field":"id"}}\')');

it('disables visibility checks')
    ->expect(new Agg(['value_count' => ['field' => 'id']], false))
    ->toBeExpression('pdb.agg(\'{"value_count":{"field":"id"}}\', false)');

it('generates a correct aggregate from a string')
    ->expect(new Agg('{"value_count": {"field": "id"}}'))
    ->toBeExpression('pdb.agg(\'{"value_count":{"field":"id"}}\')');

it('generates an aggregate as a facet')
    ->expect(new Agg(['value_count' => ['field' => 'id']], asFacet: true))
    ->toBeExpression('pdb.agg(\'{"value_count":{"field":"id"}}\') over ()');

it('panics for an invalid json string', function () {
    (new Agg('invalid json string'))->getValue(grammar());
})->throws(JsonException::class);
