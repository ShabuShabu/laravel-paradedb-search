<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\PhrasePrefix;

it('identifies documents containing a given sequence of words followed by a term prefix')
    ->expect(new PhrasePrefix('description', ['har']))
    ->toBeExpression("paradedb.phrase_prefix(field => 'description', phrases => ARRAY['har'], max_expansion => NULL::integer)");

it('identifies documents containing a given sequence of words followed by a term prefix with max_expansion enabled')
    ->expect(new PhrasePrefix('description', ['har'], 2))
    ->toBeExpression("paradedb.phrase_prefix(field => 'description', phrases => ARRAY['har'], max_expansion => 2)");
