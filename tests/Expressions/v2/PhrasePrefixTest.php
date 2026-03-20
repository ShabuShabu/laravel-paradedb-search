<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\PhrasePrefix;

pest()->group('v2', 'expressions');

it('identifies documents containing a given sequence of words followed by a term prefix')
    ->expect(new PhrasePrefix(['running', 'sh']))
    ->toBeExpression("pdb.phrase_prefix(phrases => ARRAY['running', 'sh'])");

it('identifies documents containing a given sequence of words followed by a term prefix with max_expansion enabled')
    ->expect(new PhrasePrefix(['running', 'sh'], 2))
    ->toBeExpression("pdb.phrase_prefix(phrases => ARRAY['running', 'sh'], max_expansion => 2)");
