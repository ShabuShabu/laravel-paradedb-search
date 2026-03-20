<?php

/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

use ShabuShabu\ParadeDB\Expressions\v2\SnippetPositions;

pest()->group('v2', 'expressions');

it('gets the snippet positions')
    ->expect(new SnippetPositions('description'))
    ->toBeExpression("pdb.snippet_positions(field => \"description\")");

