<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v1\PhrasePrefix;

pest()->group('v1');

it('identifies documents containing a given sequence of words followed by a term prefix')
    ->expect(new PhrasePrefix('description', ['running', 'sh']))
    ->toBeExpression("paradedb.phrase_prefix(field => 'description', phrases => ARRAY['running', 'sh'])");

it('identifies documents containing a given sequence of words followed by a term prefix with max_expansion enabled')
    ->expect(new PhrasePrefix('description', ['running', 'sh'], 2))
    ->toBeExpression("paradedb.phrase_prefix(field => 'description', phrases => ARRAY['running', 'sh'], max_expansion => 2)");
